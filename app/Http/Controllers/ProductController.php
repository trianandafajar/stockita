<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
// use Imagick;
use Intervention\Image\Laravel\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view products')->only(['index', 'show']);

        $this->middleware('permission:create products')->only(['create', 'store']);

        $this->middleware('permission:edit products')->only(['edit', 'update']);

        $this->middleware('permission:delete products')->only(['destroy']);

        $this->middleware('permission:upload product images')->only(['updateImage']);
    }

    // generate sku
    private function generateSku()
    {
        $date = now()->format('Ymd');

        $lastProduct = Product::whereDate('created_at', now())
            ->latest()
            ->first();

        if ($lastProduct) {
            $lastNumber = (int) substr($lastProduct->sku, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "PRD-$date-$newNumber";
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('store_id', Auth::user()->store->id)->where('is_active', true)->get();
        $categories = Category::where('store_id', Auth::user()->store->id)->get();

        return view('produk.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        if (! auth()->user()->canCreateProduct()) {
            return back()->with('error', 'Limit produk habis');
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            if (auth()->user()->can('upload product images')) {
                $file = $request->file('image');
                $filename = 'prd-' . time() . '.webp';

                // Pastikan folder ada
                $directory = storage_path('app/public/products');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $path = $directory . '/' . $filename;

                Image::read($file)
                    ->cover(200, 200, 'center')
                    ->toWebp(30)
                    ->save($path);

                $imagePath = 'products/' . $filename;
            } else {
                session()->flash('error', 'Gambar tidak diupload karena tidak punya izin');
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'sku' => $this->generateSku(),
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'created_by' => auth()->id(),
            'store_id' => auth()->user()->store->id,
            'warehouse_id' => $request->warehouse_id,
        ]);

        logActivity('CREATE', $product, [
            'name' => $product->name,
            'price' => $product->price,
            'category_id' => $product->category_id,
        ]);

        return redirect()->back()->with('success', 'Produk baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::where('store_id', Auth::user()->store->id)->findOrFail($id);
        $categories = Category::where('store_id', Auth::user()->store->id)->get();

        return view('produk.show', compact('product', 'categories'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',

        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 255 karakter.',

            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',

            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
        ]);

        $product = Product::findOrFail($id);

        $before = $product->only(['name', 'price', 'category_id']);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
        ]);

        logActivity('UPDATE', $product, [
            'before' => $before,
            'after' => $product->only(['name', 'price', 'category_id'])
        ]);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        $data = $product->only(['name', 'price', 'category_id']);

        $product->delete();

        logActivity('DELETE', $product, $data);

        return redirect()->back()->with('success', 'Produk berhasil dihapus!');
    }

    // import exel
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
        ], [
            'file.required' => 'File wajib diupload!',
            'file.mimes' => 'Format file harus Excel (.xlsx, .xls, .csv)',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'import')
                ->withInput();
        }
        try {
            Excel::import(new ProductsImport, $request->file('file'));

            logActivity('IMPORT', new Product(), [
                'file' => $request->file('file')->getClientOriginalName()
            ]);

            return back()->with('success', 'Data produk berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $rowNumber = $failure->row();

                $message = $failure->errors()[0];
                $cleanMessage = str_replace(':row', $rowNumber, $message);

                if (isset($failure->values()[$failure->attribute()])) {
                    $cleanMessage = str_replace(':input', $failure->values()[$failure->attribute()], $cleanMessage);
                }

                $errorMessages[] = $cleanMessage;
            }

            $fullMessage = implode("<br>", $errorMessages);

            return back()->with('error', $fullMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // update image
    public function updateImage(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $file = $request->file('image');
            $filename = 'prd-' . time() . '.webp';
            $directory = storage_path('app/public/products');

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $path = $directory . '/' . $filename;

            try {
                Image::read($file)
                    ->cover(200, 200, 'center')
                    ->toWebp(30)
                    ->save($path);

                $imagePath = 'products/' . $filename;

                $product->update([
                    'image' => $imagePath,
                ]);

                logActivity('UPDATE_IMAGE', $product, [
                    'image' => $imagePath
                ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses gambar: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Gambar produk berhasil diperbarui!');
    }
}
