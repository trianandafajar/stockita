<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Customer::with(['user', 'store']);

        if ($this->user->hasRole('admin')) {
            return $query->get();
        }

        if ($this->user->hasRole('owner')) {
            return $query->where('store_id', $this->user->store->id)->get();
        }

        return collect();
    }

    public function headings(): array
    {
        return [
            'No',
            'Name',
            'Email',
            'Phone Number',
            'Type',
            'Status',
            'Store',
            'Active Status',
            'Created At',
        ];
    }

    public function map($customer): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $customer->user->name,
            $customer->user->email,
            $customer->formatted_phone,
            $customer->type,
            $customer->status,
            $customer->store->name,
            $customer->is_active ? 'Active' : 'Inactive',
            $customer->created_at->format('d-m-Y')
        ];
    }
}
