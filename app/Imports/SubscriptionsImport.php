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

            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name'     => $row['nama'],
                    'password' => Hash::make($row['password']),
                ]
            );

            if (!$user->store) {
                $store = Store::create([
                    'name'    => $row['nama_toko'],
                    'address' => $row['alamat'] ?? null,
                    'owner_id' => $user->id,
                    'slug' => $this->generateUniqueSlug($row['nama_toko']),
                    'email'    => $row['email'],
                ]);
            } else {
                $store = $user->store;
            }

            $plan = Plan::where('name', $row['plan'])->firstOrFail();

            $subscription = $user->subscription()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => $plan->id,
                    'interval' => $row['interval'],
                    'status' => 'active',
                    'started_at' => now(),
                    'current_period_end' => $row['interval'] === 'yearly'
                        ? now()->addYear()
                        : now()->addMonth(),
                ]
            );

            $user->syncAllLimits();

            return $subscription;
        });
    }

    public function rules(): array
    {
        return [
            'nama'       => 'required|string',
            'email'      => 'required|email',
            'password'   => 'required|min:6',
            'nama_toko'  => 'required|string',
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
