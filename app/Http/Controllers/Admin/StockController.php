<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create inventory')->only(['store',]);
        $this->middleware('permission:adjust stock')->only(['update', 'reduce']);
        $this->middleware('permission:delete inventory')->only(['destroy']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ], [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Invalid product.',

            'warehouse_id.required' => 'Warehouse is required.',
            'warehouse_id.exists' => 'Invalid warehouse.',

            'qty.required' => 'Quantity is required.',
            'qty.integer' => 'Quantity must be a number.',
            'qty.min' => 'Minimum quantity is 1.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'createStock')
                ->with('open_modal', 'create-stock')
                ->withInput();
        }

        $stock = Stock::firstOrCreate(
            [
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
            ],
            [
                'qty' => 0,
                'is_demo'  => auth()->user()->is_demo,
            ]
        );

        $stock->increment('qty', $request->qty);

        logActivity('CREATE', $stock, [
            'product_id' => $stock->product_id,
            'warehouse_id' => $stock->warehouse_id,
            'qty' => $stock->qty,
        ]);

        return redirect()->back()->with('success', 'Stock added successfully!');
    }

    // tambah
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => ['required', 'integer', 'min:1'],
        ],  [
            'qty.required' => 'Quantity is required.',
            'qty.integer' => 'Quantity must be a number.',
            'qty.min' => 'Minimum quantity is 1.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'addStock')
                ->with('open_modal', 'add-stock')
                ->with('stock_id', $id)
                ->withInput();
        }

        $stock = Stock::findOrFail($id);

        $qty = $stock->qty;
        $addQty = $request->qty;
        $updateQty = $qty + $addQty;

        $stock->update([
            'qty' => $updateQty,
        ]);

        logActivity('ADD_STOCK', $stock, [
            'product_id' => $stock->product_id,
            'before' => $qty,
            'added' => $addQty,
            'after' => $updateQty,
        ]);

        return redirect()->back()->with('success', 'Stock added successfully!');
    }

    // kuarngi
    public function reduce(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'qty' => ['required', 'integer', 'min:1'],
        ], [
            'qty.required' => 'Quantity is required.',
            'qty.integer' => 'Quantity must be a number.',
            'qty.min' => 'Minimum quantity is 1.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'addStock')
                ->with('open_modal', 'add-stock')
                ->with('stock_id', $id)
                ->withInput();
        }

        $stock = Stock::findOrFail($id);

        $beforeQty = $stock->qty;
        $reduceQty = $request->qty;
        $afterQty = $beforeQty - $reduceQty;

        $stock->update([
            'qty' => $request->qty,
        ]);

        logActivity('REDUCE_STOCK', $stock, [
            'product_id' => $stock->product_id,
            'before' => $beforeQty,
            'reduced' => $afterQty,
            'after' => $reduceQty
        ]);

        return redirect()->back()->with('success', 'Stock reduced successfully!');
    }

    // hapus
    public function destroy(string $id)
    {
        $stock = Stock::findOrFail($id);

        $data = $stock->only(['product_id', 'warehouse_id', 'qty']);

        $stock->delete();

        logActivity('DELETE', $stock, $data);

        return redirect()->back()->with('success', 'Stock deleted successfully!');
    }
}
