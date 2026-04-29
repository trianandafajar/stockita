<?php

namespace App\Http\Controllers;

use App\Mail\TransactionMail;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view transactions')->only(['index', 'show']);

        $this->middleware('permission:create transactions')->only(['create', 'store']);

        $this->middleware('permission:delete transactions')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $storeId = Auth::user()->store->id;

        $transactionQuery = Transaction::where('store_id', $storeId)
            ->where('is_active', true)
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->where('invoice_code', 'like', "%$search%")
                        ->orWhereHas('customer.user', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });
                });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            });

        $stats = [
            'total' => (clone $transactionQuery)->count(),

            'total_amount' => (clone $transactionQuery)->sum('total'),

            'pending' => (clone $transactionQuery)
                ->where('status', '!=', 'paid')
                ->count(),

            'items' => TransactionItem::whereHas('transaction', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->count(),
        ];

        $transactions = $transactionQuery
            ->with('customer')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // dd($transactions);

        return view('transaksi.index', compact('transactions', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('store_id', Auth::user()->store->id)->get();

        return view('transaksi.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->canCreateTransaction()) {
            return response()->json(['message' => 'Limit transaksi habis'], 400);
        }

        DB::beginTransaction();

        try {
            if (empty($request->items)) {
                return response()->json(['message' => 'Keranjang kosong'], 422);
            }

            $invoice = 'INV-' . now()->format('YmdHis');
            $total = 0;

            foreach ($request->items as $item) {
                $total += $item['qty'] * $item['price'];
            }

            $paid = $request->paid ?? 0;
            $change = $paid - $total;

            if ($change < 0) {
                return response()->json(['message' => 'Nominal bayar kurang!'], 422);
            }

            $transaction = Transaction::create([
                'invoice_code' => $invoice,
                'customer_id' => $request->filled('customer_id') ? $request->customer_id : null,
                'customer_name' => $request->filled('customer_id') ? null : $request->customer_name,
                'total' => $total,
                'store_id' => Auth::user()->store->id,
                'payment_method' => $request->payment_method,
                'paid_at' => $request->paid_at,
                'notes' => $request->notes,
                'paid' => $paid,
                'change' => $change,
                'status' => 'paid',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $productId = $item['product_id'];
                $qty = $item['qty'];
                $price = $item['price'];

                $totalStock = Stock::where('product_id', $productId)->sum('qty');
                $product = Product::find($productId);
                if ($totalStock < $qty) {
                    DB::rollBack();

                    return response()->json([
                        'message' => "Stok produk '{$product->name}' tidak cukup",
                    ], 422);
                }

                $transactionItem = TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);

                // update stok
                $stocks = Stock::where('product_id', $productId)
                    ->where('qty', '>', 0)
                    ->orderByDesc('qty')
                    ->lockForUpdate()
                    ->get();

                $remaining = $qty;

                foreach ($stocks as $stock) {
                    if ($remaining <= 0) {
                        break;
                    }
                    $take = min($stock->qty, $remaining);
                    $stock->decrement('qty', $take);
                    $remaining -= $take;
                }
            }

            $transaction->update(['total' => $total]);

            DB::commit();

            logActivity('CREATE', $transaction, [
                'invoice' => $transaction->invoice_code,
                'total' => $transaction->total,
                'paid' => $transaction->paid,
                'change' => $transaction->change,
                'payment_method' => $transaction->payment_method,
                'store_id' => $transaction->store_id,
                'items' => collect($request->items)->map(function ($item) {
                    return [
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                    ];
                }),
            ]);

            // buat struk
            $transaction->load('items.product');
            $receiptPath = $this->generateReceipt($transaction);
            $transaction->update(['receipt' => $receiptPath]);

            if ($transaction->customer_id) {
                $email = $transaction->customer?->user?->email;

                if ($email) {
                    Mail::to($email)
                        ->send(new TransactionMail($transaction, 'transaction.success'));
                }
            }


            return response()->json([
                'message' => 'Transaksi berhasil',
                'data' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['items.product', 'customer'])
            ->findOrFail($id);

        return view('transaksi.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = Transaction::findOrFail($id);

        if ($transaksi->receipt && Storage::disk('public')->exists($transaksi->receipt)) {
            Storage::disk('public')->delete($transaksi->receipt);
        }

        $data = [
            'invoice' => $transaksi->invoice_code,
            'total' => $transaksi->total,
            'store_id' => $transaksi->store_id,
        ];

        $transaksi->delete();

        logActivity('DELETE', $transaksi, $data);

        return redirect()->back()->with('success', 'Data transaksi berhasil dihapus');
    }

    private function generateReceipt($transaction)
    {
        $storeId = Auth::user()->store->id;
        $store = Store::findOrFail($storeId);

        $driver = extension_loaded('imagick') ? new Driver() : new Driver();
        $manager = new ImageManager($driver);

        $width = 350;
        $leftX = 35;
        $center = $width / 2;
        $fontPath = public_path('fonts/RobotoMono-Regular.ttf');

        // Gunakan tempHeight yang cukup
        $tempHeight = 2000;
        $img = $manager->create($width, $tempHeight)->fill('white');

        $y = 30;

        // --- Header Toko ---
        $img->text(strtoupper($store->name ?? 'TOKO'), $center, $y, function ($font) use ($fontPath) {
            $font->file($fontPath)->size(22)->align('center')->color('000');
        });

        $y += 25;
        $address = strtoupper($store->address ?? 'ALAMAT TOKO');
        $charPerLine = floor(($width - ($leftX * 2)) / 7);
        $lines = explode("\n", wordwrap($address, $charPerLine, "\n", true));

        foreach ($lines as $line) {
            $img->text($line, $center, $y, function ($font) use ($fontPath) {
                $font->file($fontPath)->size(12)->align('center')->color('000');
            });
            $y += 16;
        }

        $y += 5;
        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;

        // --- Info Transaksi ---
        $img->text($transaction->invoice_code, $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 18;
        $img->text($transaction->created_at->format('d/m/Y H:i'), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;
        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;

        // --- Items ---
        foreach ($transaction->items as $item) {
            $name = strtoupper($item->product->name);
            $lines = receipt_wrap($name, 20);

            foreach ($lines as $i => $lineText) {
                if ($i === 0) {
                    $left = $lineText . ' x' . $item->qty;
                    $right = 'Rp ' . number_format($item->subtotal, 0, ',', '.');
                    $img->text(receipt_format($left, $right), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
                } else {
                    $img->text($lineText, $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
                }
                $y += 18;
            }
        }

        $y += 10;
        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 20;

        // --- Totals ---
        $img->text(receipt_format('TOTAL', 'Rp ' . number_format($transaction->total, 0, ',', '.')), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 18;
        $img->text(receipt_format('TUNAI', 'Rp ' . number_format($transaction->paid, 0, ',', '.')), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 18;
        $img->text(receipt_format('KEMBALI', 'Rp ' . number_format($transaction->change, 0, ',', '.')), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 25;

        $img->text(receipt_line(), $leftX, $y, fn($font) => $font->file($fontPath)->size(12)->color('000'));
        $y += 25;

        // --- Footer ---
        $img->text('TERIMA KASIH', $center, $y, fn($font) => $font->file($fontPath)->size(12)->align('center')->color('000'));
        $y += 18;
        $img->text('SELAMAT BERBELANJA KEMBALI', $center, $y, fn($font) => $font->file($fontPath)->size(11)->align('center')->color('000'));

        // --- Finalisasi Gambar ---
        $finalHeight = $y + 30;
        $img->crop($width, $finalHeight, 0, 0);

        $dir = storage_path('app/public/receipts');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileName = 'receipt-' . $transaction->id . '.webp';
        $finalPath = $dir . '/' . $fileName;

        $img->toWebp(30)->save($finalPath);

        return 'receipts/' . $fileName;
    }
}
