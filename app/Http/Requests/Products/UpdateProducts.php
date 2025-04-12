<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProducts extends FormRequest
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
        $productId = $this->route('product');
        //dd($productId);

        return [
            'name'=> 'required|string|max:200|unique:products,name,'.$productId,
            'reference'=> 'required|string|max:100|unique:products,reference,'.$productId,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,'.$productId,
            'description'=> 'required|string|max:500',
            'stock'=> 'nullable|numeric|between:0,999999.99',  // Hasta 6 enteros y 2 decimales
            'sale_price'=> 'required|numeric|between:0,9999999999.99',  // Hasta 10 enteros y 2 decimales
            'cost_price'=> 'nullable|numeric|between:0,9999999999.99',
            'visible'=> 'required|string|max:5|in:true,false',
             'image1' => [
                'nullable',
                Rule::when(request()->hasFile('image1'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
                Rule::when(!request()->has('keep_image1') && !request()->hasFile('image1'), ['prohibited'])
            ],
            'image2' => [
                'nullable',
                Rule::when(request()->hasFile('image2'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
                Rule::when(!request()->has('keep_image2') && !request()->hasFile('image2'), ['prohibited'])
            ],
            'image3' => [
                'nullable',
                Rule::when(request()->hasFile('image3'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
                Rule::when(!request()->has('keep_image3') && !request()->hasFile('image3'), ['prohibited'])
            ],
            'image4' => [
                'nullable',
                Rule::when(request()->hasFile('image4'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
                Rule::when(!request()->has('keep_image4') && !request()->hasFile('image4'), ['prohibited'])
            ],
            'image5' => [
                'nullable',
                Rule::when(request()->hasFile('image5'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
                Rule::when(!request()->has('keep_image5') && !request()->hasFile('image5'), ['prohibited'])
            ],
            'color'=> 'nullable|string|max:50',
            'categories_products_id'=> 'required|integer|exists:categories_products,id',

        ];
    }
}
