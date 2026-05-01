<?php

namespace App\Imports;

use App\Models\Plan;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubscriptionsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {

            // Find or create the user
            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name'     => $row['name'],
                    'password' => Hash::make($row['password']),
                    'is_demo'  => auth()->user()->is_demo,
                ]
            );

            // Find or create the store if the user doesn't have one
            if (!$user->store) {
                $store = Store::create([
                    'name'     => $row['store_name'],
                    'address'  => $row['address'] ?? null,
                    'owner_id' => $user->id,
                    'slug'     => $this->generateUniqueSlug($row['store_name']),
                    'email'    => $row['email'],
                    'is_demo'  => auth()->user()->is_demo,
                ]);
            } else {
                $store = $user->store;
            }

            // Get the subscription plan
            $plan = Plan::where('name', $row['plan'])->firstOrFail();

            // Create or update the subscription
            $subscription = Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id'            => $plan->id,
                    'interval'           => $row['interval'],
                    'status'             => 'active',
                    'started_at'         => now(),
                    'current_period_end' => $row['interval'] === 'yearly'
                        ? now()->addYear()
                        : now()->addMonth(),
                    'is_demo'  => auth()->user()->is_demo,
                ]
            );

            // Sync usage limits (e.g., product or employee quotas)
            $user->syncAllLimits();

            return $subscription;
        });
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string',
            'email'      => 'required|email',
            'password'   => 'required|min:6',
            'store_name' => 'required|string',
            'plan'       => 'required|exists:plans,name',
            'interval'   => 'required|in:monthly,yearly',
        ];
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Store::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
