<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view categories')->only(['index', 'show']);

        $this->middleware('permission:create categories')->only(['create', 'store']);

        $this->middleware('permission:edit categories')->only(['edit', 'update']);

        $this->middleware('permission:delete categories')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoriesQuery = Category::with('store')
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhereHas('store.owner', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('store', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });
                });
            })
            ->when($request->store, function ($q) use ($request) {
                $q->where('store_id', $request->store);
            });

        $categories = $categoriesQuery->latest()->get();
        $stores = Store::all();

        return view('admin.category.index', compact('categories', 'stores'));
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'store' => 'required',
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
            'store.required' => 'Toko wajib disi!'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => $this->generateUniqueSlug($request->name),
            'store_id' => $request->store,
        ]);

        logActivity('CREATE', $category, [
            'name' => $category->name,
            'slug' => $category->slug,
            'store_id' => $category->store_id,
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'store' => ['required'],
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
            'store.required' => 'Toko wajib diisi!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'editCategory')
                ->with('open_modal', 'edit-category')
                ->with('category_id', $id)
                ->withInput();
        }

        $before = $category->only(['name', 'slug']);


        $category->update([
            'name' => $request->name,
            'slug' => $this->generateUniqueSlug($request->name),
            'store_id' => $request->store,
        ]);

        logActivity('UPDATE', $category, [
            'before' => $before,
            'after' => $category->only(['name', 'slug'])
        ]);

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk!');
        }

        $data = $category->only(['name', 'slug', 'store_id']);

        $category->delete();

        logActivity('DELETE', $category, $data);

        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Category::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
