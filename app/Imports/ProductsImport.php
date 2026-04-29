<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $storeId;

    public function __construct($storeId = null)
    {
        $this->storeId = $storeId;
    }

    public function model(array $row)
    {
        $finalStoreId = $this->storeId ?? Auth::user()->store->id;

        $category = Category::firstOrCreate(
            ['name' => $row['kategori']],
            [
                'store_id'     => $finalStoreId,
                'slug' => $this->generateUniqueSlug($row['kategori']),
                'is_active' => true
            ]
        );

        return new Product([
            'name'         => $row['nama_produk'],
            'sku'          => $row['sku'],
            'price'        => $row['harga'],
            'image'        => null,
            'category_id'  => $category->id,
            'store_id'     => $finalStoreId,
            'created_by'   => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'sku'         => 'required|unique:products,sku',
            'harga'       => 'required|numeric',
            'kategori'    => 'required',
        ];
    }

    // pesan error
    public function customValidationMessages()
    {
        return [
            'sku.unique'   => 'Baris :row gagal diimport: SKU ":input" sudah terdaftar di sistem.',
            'sku.required' => 'Baris :row gagal: Kolom SKU tidak boleh kosong.',
            'harga.numeric' => 'Baris :row gagal: Harga harus berupa angka.',
            'nama_produk.required' => 'Baris :row gagal: Nama produk wajib diisi.',
        ];
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Category::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
