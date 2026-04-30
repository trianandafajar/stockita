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
    protected $storeId;

    public function __construct($storeId = null)
    {
        $this->storeId = $storeId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $finalStoreId = $this->storeId ?? Auth::user()->store->id;

        // Automatically create category if it doesn't exist
        $category = Category::firstOrCreate(
            ['name' => $row['category']],
            [
                'store_id'  => $finalStoreId,
                'slug'      => $this->generateUniqueSlug($row['category']),
                'is_active' => true
            ]
        );

        return new Product([
            'name'        => $row['product_name'],
            'sku'         => $row['sku'],
            'price'       => $row['price'],
            'image'       => null,
            'category_id' => $category->id,
            'store_id'    => $finalStoreId,
            'created_by'  => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'sku'          => 'required|unique:products,sku',
            'price'        => 'required|numeric',
            'category'     => 'required',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'sku.unique'           => 'Row :row failed to import: SKU ":input" is already registered in the system.',
            'sku.required'         => 'Row :row failed: SKU column cannot be empty.',
            'price.numeric'        => 'Row :row failed: Price must be a number.',
            'product_name.required' => 'Row :row failed: Product name is required.',
        ];
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Category::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
