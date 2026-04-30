<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view store')->only(['index', 'show']);
        $this->middleware('permission:create store')->only('store');
        $this->middleware('permission:edit store')->only('update');
        $this->middleware('permission:delete store')->only('destroy');
    }

    public function index()
    {
        $stores = Store::all();

        return view('admin.store.index', compact('stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_name'  => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:stores,email',
            'phone'       => 'nullable',
            'address'     => 'nullable',
        ], [
            'owner_name.required'  => 'Owner name is required!',
            'owner_email.required' => 'Owner email is required!',
            'owner_email.email'    => 'Invalid email format!',
            'owner_email.unique'   => 'Owner email is already in use!',
            'email.required'       => 'Store email is required!',
            'email.email'          => 'Invalid store email format!',
            'email.unique'         => 'Store email is already in use!',
            'name.required'        => 'Store name is required!',
        ]);

        $owner = User::create([
            'name'     => $request->owner_name,
            'email'    => $request->owner_email,
            'password' => Hash::make($request->password),
        ]);

        $owner->assignRole('owner');

        $store = Store::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'owner_id' => $owner->id,
            'address'  => $request->address,
            'phone'    => $request->phone,
            'slug'     => $this->generateUniqueSlug($request->name),
        ]);

        logActivity('CREATE', $store, [
            'store_name'  => $store->name,
            'store_email' => $store->email,
            'owner_name'  => $owner->name,
            'owner_email' => $owner->email,
        ]);

        return redirect()->back()->with('success', 'Store created successfully!');
    }

    public function show($id)
    {
        $store = Store::withCount([
            'products',
            'transactions',
            'customers',
            'warehouse'
        ])->with('owner')->findOrFail($id);

        return view('admin.store.show', compact('store'));
    }

    public function update(Request $request, $id)
    {
        $store = Store::findOrFail($id);
        $owner = User::findOrFail($store->owner_id);

        $request->validate([
            'owner_name'  => 'required|string|max:255',
            'owner_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($owner->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('stores', 'email')->ignore($store->id),
            ],
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable',
            'address'  => 'nullable',
            'password' => 'nullable|min:8', // Optional password update
        ], [
            'owner_name.required'  => 'Owner name is required!',
            'owner_email.required' => 'Owner email is required!',
            'owner_email.unique'   => 'Owner email is already in use!',
            'email.required'       => 'Store email is required!',
            'email.unique'         => 'Store email is already in use!',
            'name.required'        => 'Store name is required!',
        ]);

        $before = [
            'store_name'  => $store->name,
            'store_email' => $store->email,
            'owner_name'  => $owner->name,
            'owner_email' => $owner->email,
        ];

        // Update Owner details
        $ownerData = [
            'name'  => $request->owner_name,
            'email' => $request->owner_email,
        ];

        if ($request->filled('password')) {
            $ownerData['password'] = Hash::make($request->password);
        }

        $owner->update($ownerData);

        // Update Store details
        $store->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'address' => $request->address,
            'phone'   => $request->phone,
            // Only update slug if name changed
            'slug'    => ($store->name !== $request->name) ? $this->generateUniqueSlug($request->name) : $store->slug,
        ]);

        logActivity('UPDATE', $store, [
            'before' => $before,
            'after'  => [
                'store_name'  => $request->name,
                'store_email' => $request->email,
                'owner_name'  => $request->owner_name,
                'owner_email' => $request->owner_email,
            ]
        ]);

        return redirect()->back()->with('success', 'Store information updated successfully!');
    }

    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);

        $data = [
            'store_name'  => $store->name,
            'store_email' => $store->email,
            'owner_id'    => $store->owner_id,
        ];

        $store->delete();

        logActivity('DELETE', $store, $data);

        return redirect()->back()->with('success', 'Store deleted successfully!');
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Store::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
