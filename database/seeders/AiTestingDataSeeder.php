<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeder khusus untuk uji fitur AI Stockita
 * (rekomendasi produk, evaluasi produk paling laku, saran stok, slow-mover).
 *
 * Asumsi: DemoDataSeeder sudah dijalankan lebih dulu
 * (butuh demo user, store, warehouse, kategori).
 *
 * Jalankan:  php artisan db:seed --class=AiTestingDataSeeder
 * Reset:     php artisan demo:reset   (semua data is_demo akan terhapus)
 */
class AiTestingDataSeeder extends Seeder
{
    public function run(): void
    {
        $store     = Store::where('is_demo', true)->first();
        $owner     = User::where('email', 'owner@demo.com')->first();
        $warehouse = Warehouse::where('is_demo', true)->first();
        $customer  = Customer::where('is_demo', true)->first();

        if (!$store || !$owner || !$warehouse) {
            $this->command->error('Demo store / owner / warehouse tidak ditemukan. Jalankan DemoDataSeeder dulu.');
            return;
        }

        $categories = Category::where('is_demo', true)->get()->keyBy('slug');

        if ($categories->isEmpty()) {
            $this->command->error('Kategori demo tidak ditemukan. Jalankan DemoCategorySeeder dulu.');
            return;
        }

        DB::beginTransaction();
        try {
            $products = $this->seedProducts($categories, $warehouse->id, $owner->id, $store->id);
            $this->seedStocks($products, $warehouse->id);
            $this->seedTransactions($products, $store->id, $owner->id, $customer?->id);

            DB::commit();
            $this->command->info('AI testing data seeded: ' . count($products) . ' produk, ~120 transaksi (90 hari).');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('AI seeding gagal: ' . $e->getMessage());
        }
    }

    /**
     * @return array<int,array{id:int,price:float,popularity:int}>
     *         popularity = bobot pemilihan (besar = produk laku)
     */
    private function seedProducts($categories, int $warehouseId, int $userId, int $storeId): array
    {
        // popularity = bobot frekuensi muncul di transaksi
        $catalog = [
            // ── BEST SELLERS (popularity tinggi) ──
            ['Mineral Water 1.5L',         'AIT-BEV-001', 8000,   'beverage-products',           20],
            ['White Bread Loaf',           'AIT-FOD-002', 18000,  'food-products',               18],
            ['Instant Noodle Chicken',     'AIT-FOD-003', 4500,   'food-products',               25],
            ['Iced Coffee Latte 250ml',    'AIT-BEV-004', 12000,  'beverage-products',           17],
            ['Potato Chips BBQ',           'AIT-SNK-005', 16000,  'snacks-and-appetizers',       15],

            // ── MID SELLERS ──
            ['Chocolate Bar 60g',          'AIT-SNK-006', 9500,   'snacks-and-appetizers',       8],
            ['Orange Juice 1L',            'AIT-BEV-007', 22000,  'beverage-products',           7],
            ['Brown Rice 1kg',             'AIT-FOD-008', 28000,  'food-products',               6],
            ['Green Tea Bottled 350ml',    'AIT-BEV-009', 7500,   'beverage-products',           9],
            ['Cheese Crackers 200g',       'AIT-SNK-010', 17500,  'snacks-and-appetizers',       7],
            ['Cooking Oil 1L',             'AIT-FOD-011', 24000,  'food-products',               6],
            ['Shampoo 340ml',              'AIT-PCR-012', 32000,  'personal-hygiene-products',   5],

            // ── SLOW MOVERS (popularity rendah) ──
            ['Premium Imported Olive Oil', 'AIT-FOD-013', 145000, 'food-products',               1],
            ['Organic Quinoa 500g',        'AIT-FOD-014', 85000,  'food-products',               1],
            ['Luxury Body Wash 500ml',     'AIT-PCR-015', 95000,  'personal-hygiene-products',   1],
            ['Sparkling Wine Substitute',  'AIT-BEV-016', 120000, 'beverage-products',           1],
            ['Imported Truffle Chips',     'AIT-SNK-017', 78000,  'snacks-and-appetizers',       1],

            // ── NEW / NO HISTORY (untuk testing rekomendasi cold-start) ──
            ['Coconut Water 330ml',        'AIT-BEV-018', 11000,  'beverage-products',           3],
            ['Whole Wheat Pasta 500g',     'AIT-FOD-019', 21000,  'food-products',               3],
            ['Toothpaste Mint 150g',       'AIT-PCR-020', 19500,  'personal-hygiene-products',   4],
            ['Almond Mix 250g',            'AIT-SNK-021', 42000,  'snacks-and-appetizers',       2],
            ['Hand Sanitizer 100ml',       'AIT-PCR-022', 15000,  'personal-hygiene-products',   5],
        ];

        $products = [];

        foreach ($catalog as [$name, $sku, $price, $slug, $popularity]) {
            $categoryId = $categories[$slug]->id ?? null;

            $existing = Product::where('sku', $sku)->first();
            if ($existing) {
                $existing->delete();
            }

            $product = Product::create([
                'name'         => $name,
                'sku'          => $sku,
                'price'        => $price,
                'category_id'  => $categoryId,
                'warehouse_id' => $warehouseId,
                'created_by'   => $userId,
                'store_id'     => $storeId,
                'is_active'    => true,
                'is_demo'      => true,
            ]);

            $products[] = [
                'id'         => $product->id,
                'price'      => (float) $price,
                'popularity' => $popularity,
            ];
        }

        return $products;
    }

    /**
     * Stok sengaja divariasikan supaya AI bisa kasih saran:
     * - 0 / sangat rendah   → restock urgent
     * - rendah (1-10)        → restock saran
     * - normal               → aman
     * - overstock (>200)     → indikasi slow mover
     */
    private function seedStocks(array $products, int $warehouseId): void
    {
        foreach ($products as $i => $p) {
            // Distribusi: 2 habis, 4 rendah, 12 normal, sisanya overstock
            $qty = match (true) {
                $i < 2        => 0,
                $i < 6        => rand(1, 10),
                $i < 18       => rand(40, 150),
                default       => rand(220, 400),
            };

            Stock::updateOrCreate(
                ['product_id' => $p['id'], 'warehouse_id' => $warehouseId],
                ['qty' => $qty, 'is_demo' => true]
            );
        }
    }

    /**
     * Generate ~120 transaksi tersebar 90 hari terakhir.
     * Produk dipilih dengan bobot `popularity` agar best seller muncul lebih sering.
     */
    private function seedTransactions(array $products, int $storeId, int $userId, ?int $customerId): void
    {
        $pool = $this->buildWeightedPool($products);
        $totalTx = 120;

        for ($i = 0; $i < $totalTx; $i++) {
            $createdAt = now()->subDays(rand(0, 89))
                ->setTime(rand(8, 21), rand(0, 59), rand(0, 59));

            $itemCount = rand(1, 4);
            $pickedIds = [];
            $items = [];
            $total = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                // hindari produk duplikat dalam satu transaksi
                $product = null;
                for ($try = 0; $try < 5; $try++) {
                    $candidate = $pool[array_rand($pool)];
                    if (!in_array($candidate['id'], $pickedIds, true)) {
                        $product = $candidate;
                        break;
                    }
                }
                if (!$product) {
                    continue;
                }

                $pickedIds[] = $product['id'];
                $qty   = rand(1, 3);
                $price = $product['price'];
                $sub   = $qty * $price;

                $items[] = [
                    'product_id' => $product['id'],
                    'qty'        => $qty,
                    'price'      => $price,
                    'subtotal'   => $sub,
                ];
                $total += $sub;
            }

            if (empty($items)) {
                continue;
            }

            $paid = $total + (rand(0, 5) * 1000);

            $tx = Transaction::create([
                'invoice_code'   => 'AIT-' . strtoupper(Str::random(8)) . '-' . $i,
                'customer_id'    => $customerId,
                'customer_name'  => $customerId ? null : 'Walk-in #' . $i,
                'total'          => $total,
                'store_id'       => $storeId,
                'payment_method' => collect(['cash', 'qris', 'transfer'])->random(),
                'paid_at'        => $createdAt,
                'notes'          => 'AI testing seed',
                'paid'           => $paid,
                'change'         => $paid - $total,
                'status'         => 'paid',
                'created_by'     => $userId,
                'is_demo'        => true,
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ]);

            $rows = array_map(fn($it) => array_merge($it, [
                'transaction_id' => $tx->id,
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ]), $items);

            DB::table('transaction_items')->insert($rows);
        }
    }

    /**
     * Bangun pool produk berbobot — produk dengan popularity 20
     * akan muncul 20x di pool, popularity 1 hanya 1x.
     */
    private function buildWeightedPool(array $products): array
    {
        $pool = [];
        foreach ($products as $p) {
            for ($i = 0; $i < $p['popularity']; $i++) {
                $pool[] = $p;
            }
        }
        return $pool;
    }
}
