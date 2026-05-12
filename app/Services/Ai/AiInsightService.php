<?php

namespace App\Services\Ai;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Strategy:
 *   1. Backend = single source of truth for numbers (aggregate locally).
 *   2. AI only fills commentary fields (verdict, reason, summary, suggested_qty).
 *   3. Frontend receives ready-to-render data shapes (charts + lists).
 *
 * AI must NOT invent numbers — all numerics come from the aggregator.
 */
class AiInsightService
{
    public function __construct(private GroqClient $groq) {}

    // ─────────────────────────────────────────────────────────
    // OWNER / ADMIN
    // ─────────────────────────────────────────────────────────

    public function recommendations(?int $storeId): array
    {
        $period = $this->buildPeriodData($storeId);

        $compact = [
            'metrics' => [
                'revenue_total_rp' => $period['metrics']['revenue_total_rp'],
                'total_orders'     => $period['metrics']['total_orders'],
                'avg_order_rp'     => $period['metrics']['avg_order_rp'],
                'low_stock_count'  => $period['metrics']['low_stock_count'],
            ],
            'top_products' => array_slice($period['top_products'], 0, 5),
            'slow_movers'  => array_slice($period['slow_movers'], 0, 5),
            'low_stock'    => array_slice($period['low_stock'], 0, 8),
        ];

        $system = <<<TXT
You are a retail business analyst. Output MUST be a valid JSON object (no prose, no markdown).
Answer in English. Use ONLY the data provided — do not fabricate product names.
TXT;

        $user = "Last 30 days summary:\n"
              . $this->prettyJson($compact)
              . "\n\nReturn JSON with schema:\n"
              . '{ "summary": "1-2 sentence trend summary", '
              . '"actions": [ { "title": "short actionable title", "priority": "high|medium|low", "reason": "1 sentence rationale", "related_products": ["product names from data, optional"] } ] }'
              . "\nMaximum 5 actions, ordered by priority. Each action must be executable this week.";

        $ai = $this->callJson($system, $user);

        return $this->respond('recommendations', [
            'metrics'       => $period['metrics'],
            'top_products'  => $period['top_products'],
            'slow_movers'   => $period['slow_movers'],
            'low_stock'     => $period['low_stock'],
            'revenue_trend' => $period['revenue_trend'],
            'ai_summary'    => $ai['summary'] ?? '',
            'ai_actions'    => $ai['actions'] ?? [],
        ]);
    }

    public function topProductsEvaluation(?int $storeId): array
    {
        $since = Carbon::now()->subDays(30);

        $rows = DB::table('transaction_items as ti')
            ->join('transactions as t', 't.id', '=', 'ti.transaction_id')
            ->join('products as p',     'p.id', '=', 'ti.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('t.is_active', true)
            ->where('t.created_at', '>=', $since)
            ->when($storeId, fn($q) => $q->where('t.store_id', $storeId))
            ->select(
                'p.name',
                'c.name as category',
                DB::raw('SUM(ti.qty) as units_sold'),
                DB::raw('SUM(ti.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT t.id) as order_count'),
            )
            ->groupBy('p.name', 'c.name')
            ->orderByDesc('units_sold')
            ->limit(10)
            ->get();

        $totalUnits = (int) $rows->sum('units_sold') ?: 1;

        $products = $rows->map(fn($r) => [
            'name'        => $r->name,
            'category'    => $r->category ?? 'Uncategorized',
            'units_sold'  => (int) $r->units_sold,
            'order_count' => (int) $r->order_count,
            'revenue_rp'  => (float) $r->revenue,
            'share_pct'   => round(((int) $r->units_sold / $totalUnits) * 100, 1),
        ])->all();

        $byCategory = collect($products)
            ->groupBy('category')
            ->map(fn(Collection $g, $cat) => [
                'category'  => $cat,
                'units'     => $g->sum('units_sold'),
                'revenue'   => $g->sum('revenue_rp'),
                'share_pct' => round(($g->sum('units_sold') / $totalUnits) * 100, 1),
            ])
            ->sortByDesc('units')
            ->values()
            ->all();

        $system = <<<TXT
You are a sales analyst. Output MUST be valid JSON (no prose). Answer in English.
Use ONLY product names from the data — do not fabricate.
TXT;

        $namesPayload = array_map(
            fn($p) => ['name' => $p['name'], 'units_sold' => $p['units_sold'], 'share_pct' => $p['share_pct']],
            $products
        );

        $user = "Top 10 products (30 days):\n"
              . $this->prettyJson($namesPayload)
              . "\n\nReturn JSON:\n"
              . '{ "summary": "1 sentence overall verdict", '
              . '"products": [ { "name": "exact match from input", "verdict": "star|stable|risk", "comment": "1 short sentence (max 90 chars)" } ] }'
              . "\nverdict meanings: 'star' = top performer, 'stable' = consistent contributor, 'risk' = stock critical or declining share.";

        $ai = $this->callJson($system, $user);

        $commentMap = collect($ai['products'] ?? [])->keyBy('name');

        $products = array_map(function ($p) use ($commentMap) {
            $c = $commentMap[$p['name']] ?? null;
            return $p + [
                'verdict'    => $c['verdict'] ?? 'stable',
                'ai_comment' => $c['comment'] ?? '',
            ];
        }, $products);

        return $this->respond('top-products', [
            'products'           => $products,
            'category_breakdown' => $byCategory,
            'ai_summary'         => $ai['summary'] ?? '',
        ]);
    }

    public function stockSuggestions(?int $storeId): array
    {
        $since = Carbon::now()->subDays(30);

        $velocity = DB::table('transaction_items as ti')
            ->join('transactions as t', 't.id', '=', 'ti.transaction_id')
            ->where('t.is_active', true)
            ->where('t.created_at', '>=', $since)
            ->when($storeId, fn($q) => $q->where('t.store_id', $storeId))
            ->select('ti.product_id', DB::raw('SUM(ti.qty) as units_30d'))
            ->groupBy('ti.product_id')
            ->pluck('units_30d', 'product_id');

        $stocks = Stock::with('product:id,name,store_id')
            ->whereHas('product', function ($q) use ($storeId) {
                $q->when($storeId, fn($qq) => $qq->where('store_id', $storeId))
                  ->where('is_active', true);
            })
            ->get();

        $byProduct = $stocks
            ->groupBy('product_id')
            ->map(function (Collection $rows) use ($velocity) {
                $first   = $rows->first();
                $totalQty = (int) $rows->sum('qty');
                $units30  = (int) ($velocity[$first->product_id] ?? 0);
                $perDay   = $units30 / 30;
                $cover    = $perDay > 0 ? round($totalQty / $perDay, 1) : null;
                return [
                    'product'         => $first->product?->name,
                    'current_qty'     => $totalQty,
                    'units_sold_30d'  => $units30,
                    'avg_per_day'     => round($perDay, 2),
                    'days_of_cover'   => $cover,
                ];
            })
            ->filter(fn($x) => $x['product'])
            ->values();

        $urgent = $byProduct
            ->filter(fn($x) => $x['current_qty'] === 0 || ($x['days_of_cover'] !== null && $x['days_of_cover'] < 7))
            ->sortBy(fn($x) => $x['days_of_cover'] ?? -1)
            ->values()
            ->take(8)
            ->all();

        $suggested = $byProduct
            ->filter(fn($x) => $x['days_of_cover'] !== null && $x['days_of_cover'] >= 7 && $x['days_of_cover'] < 30)
            ->sortBy('days_of_cover')
            ->values()
            ->take(8)
            ->all();

        $overstock = $byProduct
            ->filter(fn($x) => $x['days_of_cover'] !== null && $x['days_of_cover'] > 60)
            ->sortByDesc('days_of_cover')
            ->values()
            ->take(8)
            ->all();

        $aiPayload = [
            'urgent'    => array_map(fn($x) => ['product' => $x['product'], 'current_qty' => $x['current_qty'], 'days_of_cover' => $x['days_of_cover'], 'avg_per_day' => $x['avg_per_day']], $urgent),
            'suggested' => array_map(fn($x) => ['product' => $x['product'], 'current_qty' => $x['current_qty'], 'days_of_cover' => $x['days_of_cover'], 'avg_per_day' => $x['avg_per_day']], $suggested),
            'overstock' => array_map(fn($x) => ['product' => $x['product'], 'current_qty' => $x['current_qty'], 'days_of_cover' => $x['days_of_cover'], 'avg_per_day' => $x['avg_per_day']], $overstock),
        ];

        $system = <<<TXT
You are an inventory analyst. Output MUST be valid JSON. Answer in English.
For each product, provide suggested_qty (estimated units to restock for the next 30 days,
based on avg_per_day × 30 plus a small safety buffer) and a 1-sentence reason.
TXT;

        $user = "Stock data:\n"
              . $this->prettyJson($aiPayload)
              . "\n\nReturn JSON:\n"
              . '{ "urgent":    [ { "product": "...", "suggested_qty": 50, "reason": "..." } ], '
              . '"suggested": [ ... ], '
              . '"overstock": [ { "product": "...", "reason": "promotion/clearance suggestion" } ] }';

        $ai = $this->callJson($system, $user);

        $merge = function (array $items, string $bucket) use ($ai) {
            $map = collect($ai[$bucket] ?? [])->keyBy('product');
            return array_map(function ($x) use ($map) {
                $a = $map[$x['product']] ?? null;
                return $x + [
                    'suggested_qty' => $a['suggested_qty'] ?? null,
                    'ai_reason'     => $a['reason']        ?? '',
                ];
            }, $items);
        };

        return $this->respond('stock-suggestions', [
            'urgent'    => $merge($urgent,    'urgent'),
            'suggested' => $merge($suggested, 'suggested'),
            'overstock' => $merge($overstock, 'overstock'),
            'counts'    => [
                'urgent'    => count($urgent),
                'suggested' => count($suggested),
                'overstock' => count($overstock),
            ],
        ]);
    }

    public function personalizedForBuyer(int $userId): array
    {
        $customer = Customer::where('user_id', $userId)->first();
        if (!$customer) {
            return $this->respond('personalized', [
                'empty'   => true,
                'message' => 'No customer profile found.',
            ]);
        }

        $history = DB::table('transaction_items as ti')
            ->join('transactions as t', 't.id', '=', 'ti.transaction_id')
            ->join('products as p',     'p.id', '=', 'ti.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('t.customer_id', $customer->id)
            ->select('p.id', 'p.name', 'c.name as category', DB::raw('SUM(ti.qty) as units'))
            ->groupBy('p.id', 'p.name', 'c.name')
            ->orderByDesc('units')
            ->limit(15)
            ->get();

        if ($history->isEmpty()) {
            return $this->respond('personalized', [
                'empty'   => true,
                'message' => 'No order history yet. Make a purchase first so AI can recommend products for you.',
            ]);
        }

        $purchasedIds = $history->pluck('id')->all();

        $catalog = Product::with('category:id,name')
            ->where('store_id', $customer->store_id)
            ->where('is_active', true)
            ->whereNotIn('id', $purchasedIds)
            ->limit(40)
            ->get(['id', 'name', 'price', 'category_id'])
            ->map(fn($p) => [
                'name'     => $p->name,
                'category' => $p->category?->name ?? 'Uncategorized',
                'price_rp' => (float) $p->price,
            ])
            ->all();

        $historyPayload = $history->map(fn($r) => [
            'product'  => $r->name,
            'category' => $r->category,
            'units'    => (int) $r->units,
        ])->all();

        $system = <<<TXT
You are a personal shopping assistant. Output MUST be valid JSON. Answer in English.
ONLY pick products from the provided "catalog" — do not fabricate names.
TXT;

        $user = "Customer history:\n" . $this->prettyJson($historyPayload)
              . "\n\nAvailable catalog:\n" . $this->prettyJson($catalog)
              . "\n\nReturn JSON:\n"
              . '{ "summary": "1 warm sentence", '
              . '"recommendations": [ { "product": "must be from catalog", "reason": "1 sentence why it fits" } ] }'
              . "\nPick 3-5 products most relevant to the customer's shopping habits.";

        $ai = $this->callJson($system, $user);

        $catalogMap = collect($catalog)->keyBy('name');
        $recs = collect($ai['recommendations'] ?? [])
            ->map(function ($r) use ($catalogMap) {
                $base = $catalogMap[$r['product']] ?? null;
                if (!$base) return null;
                return $base + ['ai_reason' => $r['reason'] ?? ''];
            })
            ->filter()
            ->values()
            ->all();

        return $this->respond('personalized', [
            'history'         => $historyPayload,
            'recommendations' => $recs,
            'ai_summary'      => $ai['summary'] ?? '',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // SHARED AGGREGATORS
    // ─────────────────────────────────────────────────────────

    private function buildPeriodData(?int $storeId): array
    {
        $now   = Carbon::now();
        $since = $now->copy()->subDays(30);

        $txQuery = Transaction::query()
            ->where('is_active', true)
            ->where('created_at', '>=', $since)
            ->when($storeId, fn($q) => $q->where('store_id', $storeId));

        $totalRevenue = (float) (clone $txQuery)->sum('total');
        $totalOrders  = (clone $txQuery)->count();
        $avgOrder     = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        $daily = (clone $txQuery)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total) as rev'))
            ->groupBy('day')
            ->pluck('rev', 'day');

        $trend = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->format('Y-m-d');
            $trend[] = [
                'date'    => $day,
                'label'   => Carbon::parse($day)->format('d M'),
                'revenue' => (float) ($daily[$day] ?? 0),
            ];
        }

        $itemQuery = DB::table('transaction_items as ti')
            ->join('transactions as t', 't.id', '=', 'ti.transaction_id')
            ->join('products as p',     'p.id', '=', 'ti.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('t.is_active', true)
            ->where('t.created_at', '>=', $since)
            ->when($storeId, fn($q) => $q->where('t.store_id', $storeId))
            ->select(
                'p.name',
                'c.name as category',
                DB::raw('SUM(ti.qty) as units_sold'),
                DB::raw('SUM(ti.subtotal) as revenue'),
            )
            ->groupBy('p.name', 'c.name');

        $top = (clone $itemQuery)->orderByDesc('units_sold')->limit(5)->get();
        $bot = (clone $itemQuery)->orderBy('units_sold')->limit(5)->get();

        $topProducts = $top->map(fn($r) => [
            'name'       => $r->name,
            'category'   => $r->category ?? 'Uncategorized',
            'units_sold' => (int) $r->units_sold,
            'revenue_rp' => (float) $r->revenue,
        ])->all();

        $slowMovers = $bot->map(fn($r) => [
            'name'       => $r->name,
            'category'   => $r->category ?? 'Uncategorized',
            'units_sold' => (int) $r->units_sold,
        ])->all();

        $lowStock = Stock::with('product:id,name,store_id')
            ->whereHas('product', fn($q) => $q->when($storeId, fn($qq) => $qq->where('store_id', $storeId)))
            ->where('qty', '<=', 10)
            ->get()
            ->map(fn($s) => ['name' => $s->product?->name, 'qty' => (int) $s->qty])
            ->filter(fn($x) => $x['name'])
            ->sortBy('qty')
            ->values()
            ->all();

        return [
            'metrics' => [
                'revenue_total_rp' => $totalRevenue,
                'total_orders'     => $totalOrders,
                'avg_order_rp'     => $avgOrder,
                'low_stock_count'  => count($lowStock),
            ],
            'top_products'  => $topProducts,
            'slow_movers'   => $slowMovers,
            'low_stock'     => $lowStock,
            'revenue_trend' => $trend,
        ];
    }

    // ─────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────

    private function callJson(string $system, string $user): array
    {
        $raw = $this->groq->chat($system, $user, ['json' => true, 'temperature' => 0.3, 'max_tokens' => 1200]);
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('AI returned non-JSON: ' . substr($raw, 0, 200));
        }
        return $decoded;
    }

    private function respond(string $type, array $data): array
    {
        return [
            'success'      => true,
            'type'         => $type,
            'data'         => $data,
            'generated_at' => now()->toDateTimeString(),
        ];
    }

    private function prettyJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
