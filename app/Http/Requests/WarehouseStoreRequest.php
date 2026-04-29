<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WarehouseStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string'],
            'description' => ['required', 'string'],
            
            'store_id' => [
                    $this->isMethod('post') ? Rule::requiredIf(Auth::user()->hasRole('admin')) : 'nullable',
                    'exists:stores,id'
                ],
            ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama gudang wajib diisi.',
            'name.max' => 'Nama gudang maksimal 255 karakter.',

            'location.required' => 'Lokasi wajib diisi.',

            'description.required' => 'Deskripsi wajib diisi.',

            'store_id.required' => 'Toko wajib dipilih.',
            'store_id.exists' => 'Toko yang dipilih tidak valid.',
        ];
    }
}
