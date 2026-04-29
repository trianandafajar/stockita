<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseStoreRequest;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view warehouse')->only(['index', 'show']);
        $this->middleware('permission:create warehouse')->only(['store']);
        $this->middleware('permission:edit warehouse')->only(['update']);
        $this->middleware('permission:delete warehouse')->only(['destroy']);
    }

    // generate code
    private function generateWarehouseCode()
    {
        $prefix = 'GDG-';

        $lastRecord = Warehouse::where('is_active', true)
            ->orderBy('code', 'desc')->first();
        if (! $lastRecord) {
            return $prefix . '0001';
        }
        $lastNumber = substr($lastRecord->code, strlen($prefix));
        $nextNumber = (int) $lastNumber + 1;
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouses = Warehouse::where('store_id', Auth::user()->store->id)->get();

        return view('warehouse.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('warehouse.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseStoreRequest $request)
    {
        if (! auth()->user()->canCreateWarehouse()) {
            return back()->with('error', 'Limit Gudang habis');
        }

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'code' => $this->generateWarehouseCode(),
            'location' => $request->location,
            'store_id' => Auth::user()->store->id,
            'description' => $request->description,
        ]);

        logActivity('CREATE', $warehouse, [
            'name' => $warehouse->name,
            'code' => $warehouse->code,
            'location' => $warehouse->location,
            'description' => $warehouse->description,
            'store_id' => $warehouse->store_id,
        ]);


        return redirect()->back()->with('success', 'Gudang berhasil disi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $warehouse = Warehouse::where('store_id', Auth::user()->store->id)->findOrFail($id);

        $products = Product::where('store_id', Auth::user()->store->id)->get();

        $stocks = Stock::with('product')
            ->where('warehouse_id', $id)
            ->get();

        return view('warehouse.show', compact('warehouse', 'stocks', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WarehouseStoreRequest $request, string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $before = $warehouse->only(['name', 'location', 'description']);

        $warehouse->update([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        logActivity('UPDATE', $warehouse, [
            'before' => $before,
            'after' => $warehouse->only(['name', 'location', 'description'])
        ]);

        return redirect()->back()->with('success', 'Gudang Berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $data = $warehouse->only(['name', 'location', 'description', 'store_id']);

        $warehouse->delete();

        logActivity('DELETE', $warehouse, $data);

        return redirect()->back()->with('success', 'Gudang Berhasil dihapus!');
    }
}
