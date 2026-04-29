<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[Fillable(['name', 'email', 'password',])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, HasPushSubscriptions;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // url dashboard
    public function getDashboardUrl()
    {
        if ($this->hasRole('admin')) return '/admin/dashboard';
        if ($this->hasRole('buyer')) return '/buyer/dashboard';

        return '/dashboard';
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('current_period_end', '>', now());
    }

    public function hasActiveSubscription()
    {
        if (! $this->hasRole('owner')) {
            return true;
        }

        return $this->activeSubscription()->exists();
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Store::class,
            'owner_id',
            'store_id',
            'id',
            'id'
        );
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function midtransTransaction()
    {
        return $this->hasMany(MidtransTransaction::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'owner_id');
    }

    public function transactions()
    {
        return $this->store ? $this->store->transactions() : collect();
    }

    // batas subsribe
    private function isUnlimited($limit)
    {
        return is_null($limit);
    }

    public function canCreateProduct()
    {
        $subscription = $this->subscription;

        if (! $subscription || $subscription->status !== 'active') {
            return false;
        }

        $limit = $subscription->plan->max_products;

        if ($this->isUnlimited($limit)) {
            return true;
        }

        return $this->products()->where('is_active', true)->count() < $limit;
    }

    public function canCreateTransaction()
    {
        $subscription = $this->subscription;

        // cek subcribtion aktif
        if (! $subscription || $subscription->status !== 'active') {
            return false;
        }

        $limit = $subscription->plan->max_orders;

        if ($this->isUnlimited($limit)) {
            return true;
        }

        // 
        $transactionCount = $this->transactions()->where('is_active', true)->count();

        return $transactionCount < $limit;
    }

    public function canCreateWarehouse()
    {
        $subscription = $this->subscription;

        if (! $subscription || $subscription->status !== 'active') {
            return false;
        }

        $limit = $subscription->plan->max_warehouses;

        if ($this->isUnlimited($limit)) {
            return true;
        }

        // hitung gudang
        $warehouseCount = $this->store->warehouse()->where('is_active', true)->count();

        return $warehouseCount < $limit;
    }

    public function canCreateCategories()
    {
        $subscription = $this->subscription;

        if (! $subscription || $subscription->status !== 'active') {
            return false;
        }

        $limit = $subscription->plan->max_categories;

        if ($this->isUnlimited($limit)) {
            return true;
        }

        $categoriesCount = $this->store->categories()->where('is_active', true)->count();

        // Bandingkan dengan limit plan
        return $categoriesCount < $limit;
    }

    public function canCreateCustomers()
    {
        $subscription = $this->subscription;

        if (! $subscription || $subscription->status !== 'active') {
            return false;
        }

        $limit = $subscription->plan->max_customers;

        if ($this->isUnlimited($limit)) {
            return true;
        }

        $customersCount = $this->store->customers()->where('is_active', true)->count();

        return $customersCount < $limit;
    }

    // sync syncProductsLimit
    private function syncLimit($query, $limit)
    {
        $items = $query->orderBy('created_at', 'asc')->get();

        foreach ($items as $index => $item) {
            if ($this->isUnlimited($limit)) {
                $item->is_active = true;
            } else {
                $item->is_active = $index < $limit;
            }
            $item->save();
        }
    }

    public function syncAllLimits()
    {
        if (! $this->store) {
            return;
        }

        $subscription = $this->subscription()->with('plan')->first();

        if (! $subscription || $subscription->status !== 'active') {
            return;
        }

        $plan = $subscription->plan;

        // produk
        $this->syncLimit(
            $this->products(),
            $plan->max_products
        );

        // kategori
        $this->syncLimit(
            $this->store->categories(),
            $plan->max_categories
        );

        // gudang
        $this->syncLimit(
            $this->store->warehouse(),
            $plan->max_warehouses
        );

        // customer
        $this->syncLimit(
            $this->store->customers(),
            $plan->max_customers
        );

        // transaksi
        $this->syncLimit(
            $this->transactions(),
            $plan->max_orders
        );
    }

    public function deactivateAllData()
    {
        if (! $this->store) return;

        $this->products()->update(['is_active' => false]);

        $this->store->categories()->update(['is_active' => false]);

        $this->store->warehouse()->update(['is_active' => false]);

        $this->store->customers()->update(['is_active' => false]);

        $this->transactions()->update(['is_active' => false]);
    }
}
