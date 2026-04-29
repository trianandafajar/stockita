<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest;
use App\Imports\CustomerImport;
use App\Mail\SendCustomerEmail;
use App\Models\Customer;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view customers')->only(['index', 'show']);

        $this->middleware('permission:create customers')->only(['create', 'store']);

        $this->middleware('permission:edit customers')->only(['edit', 'update']);

        $this->middleware('permission:delete customers')->only(['destroy']);

        $this->middleware('permission:send customer email')->only(['sendEmail']);
    }

    public function index(Request $request)
    {
        $customers = Customer::filter([
            'search' => $request->search,
            'type' => $request->type,
            'status' => $request->status,
        ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Customer::count(),
            'exclusive' => Customer::exclusive()->count(),
            'active' => Customer::active()->count(),
            'total_spent' => Transaction::where('status', 'paid')
                ->sum('total'),
        ];

        $stores = Store::all();

        return view('admin.pelanggan.index', compact('customers', 'stats', 'stores'));
    }

    // tambah
    public function store(CustomerStoreRequest $request)
    {
        $phone = preg_replace('/[^0-9+]/', '', $request->phone);
        if (! str_starts_with($phone, '+')) {
            if (str_starts_with($phone, '0')) {
                $phone = '+62' . substr($phone, 1);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('buyer');

        $customer = Customer::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'status' => $request->status,
            'phone' => $phone,
            'store_id' => $request->store_id,
        ]);

        logActivity('CREATE', $customer, [
            'name' => $user->name,
            'email' => $user->email,
            'type' => $customer->type,
            'status' => $customer->status,
        ]);

        return redirect()->back()->with('success', 'Pelanggan berhasil disimpan!');
    }

    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        $totalOrders = Transaction::where('customer_id', $customer->id)
            ->where('status', 'paid')
            ->count();
        $totalSpent = Transaction::where('customer_id', $customer->id)
            ->where('status', 'paid')
            ->sum('total');

        $lastOrder = optional(Transaction::where('customer_id', $customer->id)
            ->latest()
            ->first())->created_at;
        $orders = Transaction::where('customer_id', $customer->id)->get();

        // dd($orders);

        return view('admin.pelanggan.show', compact('customer', 'totalOrders', 'totalSpent', 'lastOrder', 'orders'));
    }

    public function update(CustomerStoreRequest $request, string $id)
    {
        $customer = Customer::findOrFail($id);
        $user = User::findOrFail($customer->user->id);

        $phone = preg_replace('/[^0-9+]/', '', $request->phone);
        if (! str_starts_with($phone, '+')) {
            if (str_starts_with($phone, '0')) {
                $phone = '+62' . substr($phone, 1);
            }
        }

        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'type' => $customer->type,
            'status' => $customer->status,
            'phone' => $customer->phone,
        ];

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $customer->update([
            'type' => $request->type,
            'status' => $request->status,
            'phone' => $phone,
        ]);

        logActivity('UPDATE', $customer, [
            'before' => $before,
            'after' => [
                'name' => $request->name,
                'email' => $request->email,
                'type' => $request->type,
                'status' => $request->status,
                'phone' => $phone,
            ]
        ]);

        return redirect()->back()->with('success', 'Pelanggan berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $user = User::findOrFail($customer->user->id);

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'type' => $customer->type,
        ];

        $user->delete();

        logActivity('DELETE', $customer, $data);

        return redirect()->back()->with('success', 'Pelanggan berhasil dihapus!');
    }

    // kirim email
    public function sendEmail($id)
    {
        $customer = Customer::findOrFail($id);

        Mail::to($customer->user->email)
            ->send(new SendCustomerEmail($customer));

        logActivity('SEND_EMAIL_CUSTOMER', $customer, [
            'email' => $customer->user->email,
            'customer_name' => $customer->user->name,
        ]);

        return back()->with('success', 'Email berhasil dikirim!');
    }

    // export
    public function export()
    {
        return Excel::download(new CustomerExport(auth()->user()), 'daftar-pelanggan.xlsx');
    }

    public function template()
    {
        return Excel::download(new class implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings
        {
            public function headings(): array
            {
                return [
                    'nama',
                    'email',
                    'no_telepon',
                    'alamat',
                ];
            }

            public function array(): array
            {
                return [
                    [
                        'Budi Santoso',
                        'budi@email.com',
                        '081234567890',
                        'Jl. Contoh No.1',
                    ],
                    [
                        'Siti Aminah',
                        'siti@email.com',
                        '+6281234567890',
                        'Bandung',
                    ]
                ];
            }
        }, 'template-customer.xlsx');
    }

    // import exel
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'store_id' => 'required'
        ], [
            'file.required' => 'File wajib diupload!',
            'file.mimes' => 'Format file harus Excel (.xlsx, .xls, .csv)',
            'store_id.required' => 'Toko harus dipilih!'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'import')
                ->withInput();
        }


        try {
            Excel::import(new CustomerImport($request->store_id), $request->file('file'));
            return back()->with('success', 'Data pelanggan berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $rowNumber = $failure->row();

                $message = $failure->errors()[0];
                $cleanMessage = str_replace(':row', $rowNumber, $message);

                if (isset($failure->values()[$failure->attribute()])) {
                    $cleanMessage = str_replace(':input', $failure->values()[$failure->attribute()], $cleanMessage);
                }

                $errorMessages[] = $cleanMessage;
            }

            $fullMessage = implode("<br>", $errorMessages);

            return back()->with('error', $fullMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
