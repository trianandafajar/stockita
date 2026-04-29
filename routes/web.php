<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\WarehouseController as AdminWarehouseController;
use App\Http\Controllers\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\CategoryController;
// Controllers
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Midtrans\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WarehouseController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\User;
// Buyer
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', fn() => view('welcome'));

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/pay', [PaymentController::class, 'pay']);
Route::post('/midtrans/webhook', [PaymentController::class, 'webhook']);

Route::view('/privacy', 'privacy');
Route::view('/terms', 'terms');
Route::view('/dmca', 'dmca');

Route::get('/blog/{slug}', function ($slug) {

    $articles = config('blog');

    if (! isset($articles[$slug])) {
        abort(404);
    }

    return view('blogs.detail', [
        'title' => $articles[$slug]['title'],
        'content' => $articles[$slug]['content'],
        'image' => $articles[$slug]['image'],
    ]);
});

Route::get('/features/{slug}', function ($slug) {

    $features = config('features');

    abort_if(! isset($features[$slug]), 404);

    return view('features.detail', [
        'title' => $features[$slug]['title'],
        'excerpt' => $features[$slug]['excerpt'],
        'image' => $features[$slug]['image'],
        'description' => $features[$slug]['description'],
        'steps' => $features[$slug]['steps'],
        'benefits' => $features[$slug]['benefits'],
        'faqs' => $features[$slug]['faqs'],
        'use_cases' => $features[$slug]['use_cases'],
        'highlights' => $features[$slug]['highlights'],
    ]);
});

// serach 
Route::get('/search', function (Request $request) {
    $query = strtolower($request->get('q'));
    $isAjax = $request->get('ajax');

    $allBlogs = config('blog');
    $allFeatures = config('features');

    $results = [];

    foreach ($allBlogs as $slug => $item) {
        if (str_contains(strtolower($item['title']), $query) || str_contains(strtolower($item['content']), $query)) {
            $results[] = [
                'title' => $item['title'],
                'excerpt' => Str::limit(strip_tags($item['content']), 150),
                'link' => "/blog/{$slug}",
                'image' => $item['image'],
                'type' => 'Panduan'
            ];
        }
    }

    foreach ($allFeatures as $slug => $item) {
        if (str_contains(strtolower($item['title']), $query) || str_contains(strtolower($item['description']), $query)) {
            $results[] = [
                'title' => $item['title'],
                'excerpt' => $item['excerpt'],
                'link' => "/features/{$slug}",
                'image' => $item['image'],
                'type' => 'Fitur'
            ];
        }
    }

    if ($isAjax) {
        return response()->json($results);
    }

    return view('search-results', [
        'results' => $results,
        'query' => $request->get('q')
    ]);
});

/*
|--------------------------------------------------------------------------
| Auth Routes (tanpa subscription)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // checkout
    Route::get('/checkout', [PaymentController::class, 'checkout']);

    // Subscription (HARUS bisa diakses walau belum aktif)
    Route::get('/subscription', [PaymentController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/upgrade', [PaymentController::class, 'upgrade'])->name('subscription.upgrade');

    Route::post('/settings', [SettingController::class, 'update']);

    // search transaksi
    Route::get('/products/by-store', function () {
        $storeId = request('store_id');
        $products = \App\Models\Product::with('stocks')
            ->where('store_id', $storeId)->get();
        return response()->json($products);
    });

    Route::get('/customers/search', function (Request $request) {
        return Customer::with('user')->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->q . '%');
        })->limit(5)->get()->map(function ($customer) {
            return ['id' => $customer->id, 'name' => $customer->user->name];
        });
    });

    Route::get('/customers/by-store', [AdminTransactionController::class, 'byStore']);

    //notif
    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    });

    Route::delete('/notifications/{id}', function ($id) {
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->delete();

        return response()->json(['success' => true]);
    });

    Route::post('/push/subscribe', [PushController::class, 'subscribe']);
    Route::delete('/push/unsubscribe', [PushController::class, 'unsubscribe']);

    // export
    Route::get('/transactions/export', [AdminTransactionController::class, 'export'])->name('transactions.export');
    Route::get('/product/export', [AdminProductController::class, 'export'])->name('products.export');
    Route::get('/customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');

    // logs
    Route::get('/logs', [ActivityLogController::class, 'index'])
        ->name('logs.index');
    // ->middleware('permission:view logs');

    // import
    Route::get('/subscriptions/template', [SubscriptionController::class, 'template'])->name('subscriptions.template');
    Route::post('/subscriptions/import', [SubscriptionController::class, 'import'])->name('subscriptions.import');

    Route::get('/customers/template', [AdminCustomerController::class, 'template'])->name('customers.template');
    Route::get('/products/template', [AdminProductController::class, 'template'])->name('products.template');

    // email
    Route::put('/email-template/{key}', [SettingController::class, 'updateEmail'])
        ->name('email-template.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resources([
            'categories' => AdminCategoryController::class,
            'products' => AdminProductController::class,
            'warehouse' => AdminWarehouseController::class,
            'customers' => AdminCustomerController::class,
            'store' => StoreController::class,
            'transactions' => AdminTransactionController::class,
            'roles' => RoleController::class,
            'subscriptions' => SubscriptionController::class,
        ]);

        Route::patch('/subscriptions/{id}/toggle', [SubscriptionController::class, 'toggle'])
            ->name('subscriptions.toggle');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/settings', [AdminSettingController::class, 'update']);

        Route::put('/product/update-img/{id}', [AdminProductController::class, 'updateImage'])
            ->name('products.update-image');

        Route::post('/stocks', [AdminStockController::class, 'store'])->name('stocks.store');
        Route::put('/stocks/{id}', [AdminStockController::class, 'update'])->name('stocks.update');
        Route::put('/stocks/{id}/reduce', [AdminStockController::class, 'reduce'])->name('stocks.reduce');
        Route::delete('/stocks/{id}', [AdminStockController::class, 'destroy'])->name('stocks.delete');

        // plans
        Route::put('/plans/{id}', [AdminSettingController::class, 'updatePlan']);

        // kirim email
        Route::post('/customers/{id}/send-email', [CustomerController::class, 'sendEmail']);

        Route::get('/categories-by-store/{store}', function ($storeId) {
            return Category::where('store_id', $storeId)->get();
        });

        // import exel 
        Route::post('/products/import', [AdminProductController::class, 'import'])->name('products.import');
        Route::post('/customers/import', [AdminCustomerController::class, 'import'])->name('customers.import');
    });

/*
|--------------------------------------------------------------------------
| Owner (BUTUH subscription)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner', 'subscription.active'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/customers/search', function (Request $request) {
        return Customer::with('user')->where('status', 'active')
            ->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            })
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->user->name
                ];
            });
    });

    // Resources
    Route::resources([
        'products' => ProductController::class,
        'categories' => CategoryController::class,
        'warehouse' => WarehouseController::class,
        'customers' => CustomerController::class,
        'store' => StoreController::class,
        'roles' => RoleController::class,
        'transactions' => TransactionController::class,
        'subscriptions' => SubscriptionController::class,
    ]);

    // Custom Actions
    Route::post('/customers/{id}/send-email', [CustomerController::class, 'sendEmail'])
        ->name('customers.sendEmail');

    Route::put('/product/update-img/{id}', [ProductController::class, 'updateImage'])
        ->name('products.update-image');

    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::put('/stocks/{id}', [StockController::class, 'update'])->name('stocks.update');
    Route::put('/stocks/{id}/reduce', [StockController::class, 'reduce'])->name('stocks.reduce');
    Route::delete('/stocks/{id}', [StockController::class, 'destroy'])->name('stocks.delete');

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);

    Route::put('/settings/store/{id}', [SettingController::class, 'updateStore']);

    // import exel 
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import');
});

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:buyer'])
    ->prefix('buyer')
    ->name('buyer.')
    ->group(function () {

        Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    });

require __DIR__ . '/auth.php';
