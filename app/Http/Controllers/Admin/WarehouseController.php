<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseStoreRequest;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view warehouse')->only(['index', 'show']);
        $this->middleware('permission:create warehouse')->only(['store']);
        $this->middleware('permission:edit warehouse')->only(['update']);
        $this->middleware('permission:delete warehouse')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $warehouseQuery = Warehouse::with('store')
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->where('location', 'like', "%$search%")
                        ->orWhereHas('store', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });
                });
            })
            ->when($request->store, function ($q) use ($request) {
                $q->where('store_id', $request->store);
            });

        $warehouses = $warehouseQuery->latest()->get();
        $stores = Store::all();

        return view('admin.warehouse.index', compact('warehouses', 'stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseStoreRequest $request)
    {

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'code' => $this->generateWarehouseCode(),
            'location' => $request->location,
            'store_id' => $request->store_id,
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
        $warehouse = Warehouse::findOrFail($id);

        $products = Product::where('store_id', $warehouse->store->id)->get();

        $stocks = Stock::with('product')
            ->where('warehouse_id', $id)
            ->get();

        return view('admin.warehouse.show', compact('warehouse', 'stocks', 'products'));
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

    // generate code
    private function generateWarehouseCode()
    {
        $prefix = 'GDG-';

        $lastRecord = Warehouse::orderBy('code', 'desc')->first();
        if (! $lastRecord) {
            return $prefix . '0001';
        }
        $lastNumber = substr($lastRecord->code, strlen($prefix));
        $nextNumber = (int) $lastNumber + 1;
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }
}
