<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $storeId;

    public function __construct($storeId = null)
    {
        $this->storeId = $storeId;
    }

    public function model(array $row)
    {
        $finalStoreId = $this->storeId ?? Auth::user()->store->id;

        $rawPhone = (string) ($row['no_telepon'] ?? '');

        // sanitize
        $phone = preg_replace('/[^0-9+]/', '', $rawPhone);
        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+62')) {
            $phone = '+62' . $phone;
        }

        // create user
        $user = User::create([
            'name'     => $row['nama'],
            'email'    => $row['email'],
            'password' => Hash::make('password'),
            'store_id' => $finalStoreId,
        ]);

        $user->assignRole('buyer');

        // create customer
        return new Customer([
            'user_id'  => $user->id,
            'phone'    => $phone,
            'address'  => $row['alamat'] ?? null,
            'store_id' => $finalStoreId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'alamat'      => 'nullable|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Baris :row gagal: Nama wajib diisi.',
            'email.required' => 'Baris :row gagal: Email wajib diisi.',
            'email.email' => 'Baris :row gagal: Format email tidak valid.',
            'email.unique' => 'Baris :row gagal: Email ":input" sudah digunakan.',
        ];
    }
}
