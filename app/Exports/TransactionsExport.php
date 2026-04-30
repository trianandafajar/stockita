<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = Transaction::with(['items.product', 'customer', 'store']);

        if ($this->user->hasRole('admin')) {
            return $query->get();
        }
        if ($this->user->hasRole('owner')) {
            return $query->where('store_id', $this->user->store->id)->get();
        }

        return collect();
    }

    /**
     * Define Excel Headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Invoice Code',
            'Customer Name',
            'Purchased Items',
            'Amount Paid',
            'Change',
            'Total Transaction',
            'Status',
            'Payment Method',
            'Payment Time',
            'Notes',
            'Active Status',
            'Created At',
            'Store'
        ];
    }

    /**
     * Map data rows
     */
    public function map($transaction): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Map items to a string format: Product Name (Qty)
        $itemDetails = $transaction->items->map(function ($item) {
            $productName = $item->product->name ?? $item->product_name ?? 'Product';
            return $productName . ' (' . $item->qty . ')';
        })->implode(', ');

        return [
            $rowNumber,
            $transaction->invoice_code,
            $transaction->customer_name ?? $transaction->customer?->name ?? '-',
            $itemDetails,
            $transaction->paid,
            $transaction->change,
            $transaction->total,
            strtoupper($transaction->status),
            ucfirst($transaction->payment_method ?? '-'),
            $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('d-m-Y H:i') : '-',
            $transaction->notes ?? '-',
            $transaction->is_active ? 'Active' : 'Inactive',
            $transaction->created_at->format('d-m-Y H:i'),
            $transaction->store?->name ?? '-',
        ];
    }
}
