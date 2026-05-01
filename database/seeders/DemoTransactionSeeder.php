<?php

namespace Database\Seeders;

use App\HasReceipt;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DemoTransactionSeeder extends Seeder
{
    use HasReceipt;

    public function run(): void
    {
        // 1. Ambil User (Merchant/Admin) yang memiliki Store
        $user = User::where('is_demo', true)->has('store')->first();

        if (!$user) {
            $this->command->warn('No demo user with store found. Skipping TransactionSeeder.');
            return;
        }

        // Login secara programmatik agar auth()->user() di controller berfungsi
        Auth::login($user);

        // 2. Ambil beberapa produk demo yang punya stok
        $products = Product::where('is_demo', true)
            ->whereHas('stocks', function ($q) {
                $q->where('qty', '>', 10);
            })
            ->take(3)
            ->get();

        $customer = Customer::where('is_demo', true)->first();

        // 3. Simulasikan data "Request"
        $items = [];
        $total = 0;

        foreach ($products as $product) {
            $qty = rand(1, 2);
            $price = $product->price;
            $items[] = [
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $price
            ];
            $total += ($qty * $price);
        }

        $paid = $total + 5000; // Simulasikan uang bayar lebih

        // 4. Jalankan logika yang mirip dengan Controller
        DB::beginTransaction();
        try {
            $invoice = 'INV-' . now()->format('YmdHis');

            $transaction = Transaction::create([
                'invoice_code' => $invoice,
                'customer_id' => $customer?->id,
                'customer_name' => $customer ? null : 'Guest Customer',
                'total' => $total,
                'store_id' => $user->store->id,
                'payment_method' => 'cash',
                'paid_at' => now(),
                'notes' => 'Demo transaction seed',
                'paid' => $paid,
                'change' => $paid - $total,
                'status' => 'paid',
                'created_by' => $user->id,
                'is_demo' => true,
            ]);

            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                    'is_demo' => true,
                ]);

                // Update Stock (Mengikuti logika decrement di controller)
                $stocks = Stock::where('product_id', $item['product_id'])
                    ->where('qty', '>', 0)
                    ->orderByDesc('qty')
                    ->get();

                $remaining = $item['qty'];
                foreach ($stocks as $stock) {
                    if ($remaining <= 0) break;
                    $take = min($stock->qty, $remaining);
                    $stock->decrement('qty', $take);
                    $remaining -= $take;
                }
            }

            $path = $this->generateReceipt($transaction->load('items.product'));

            // 3. Simpan pathnya
            $transaction->update(['receipt' => $path]);

            DB::commit();
            $this->command->info('Transaction seeded successfully: ' . $invoice);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Transaction seeding failed: ' . $e->getMessage());
        }
    }
}
