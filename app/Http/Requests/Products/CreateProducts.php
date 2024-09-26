<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateProducts extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=> 'required|string|max:200|unique:products',
            'reference'=> 'required|string|max:100|unique:products',
            'barcode' => 'nullable|string|max:100|unique:products',
            'description'=> 'required|string|max:500',
            'stock'=> 'required|numeric|between:0,999999.99',  // Hasta 6 enteros y 2 decimales
            'sale_price'=> 'required|numeric|between:0,9999999999.99',  // Hasta 10 enteros y 2 decimales
            'cost_price'=> 'required|numeric|between:0,9999999999.99',
            'image1'=>  'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image2'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image3'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image4'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image5'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'color'=> 'nullable|string|max:50',
            'categories_products_id'=> 'required|integer|exists:categories_products,id',
        ];
    }
}
