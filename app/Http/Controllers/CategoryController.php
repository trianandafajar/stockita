<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Category::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('store_id', Auth::user()->store->id)->where('is_active', true)->latest()->get();

        return view('category.index', compact('categories'));
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
        if (! auth()->user()->canCreateCategories()) {
            return back()->with('error', 'Limit Kategori habis');
        }

        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => $this->generateUniqueSlug($request->name),
            'store_id' => Auth::user()->store->id,
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
        ], [
            'name.required' => 'Nama kategori wajib diisi!',
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
}
