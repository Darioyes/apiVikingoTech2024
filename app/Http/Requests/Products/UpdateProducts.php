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
            'description'=> 'required|string|max:1000',
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

     public function messages(): array{
         return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.string' => 'El nombre del producto debe ser una cadena de texto.',
            'name.max' => 'El nombre del producto no debe exceder los 200 caracteres.',
            'name.unique' => 'El nombre del producto ya existe.',

            'reference.required' => 'La referencia del producto es obligatoria.',
            'reference.string' => 'La referencia del producto debe ser una cadena de texto.',
            'reference.max' => 'La referencia del producto no debe exceder los 100 caracteres.',
            'reference.unique' => 'La referencia del producto ya existe.',

            'barcode.string' => 'El código de barras debe ser una cadena de texto.',
            'barcode.max' => 'El código de barras no debe exceder los 100 caracteres.',
            'barcode.unique' => 'El código de barras ya existe.',

            'description.required' => 'La descripción del producto es obligatoria.',
            'description.string' => 'La descripción del producto debe ser una cadena de texto.',
            'description.max' => 'La descripción del producto no debe exceder los 1000 caracteres.',

            'stock.numeric' => 'El stock debe ser un número.',
            'stock.between' => 'El stock debe estar entre 0 y 999999.99.',

            'sale_price.required' => 'El precio de venta es obligatorio.',
            'sale_price.numeric' => 'El precio de venta debe ser un número.',
            'sale_price.between' => 'El precio de venta debe estar entre 0 y 9999999999.99.',

            'cost_price.numeric' => 'El precio de costo debe ser un número.',
            'cost_price.between' => 'El precio de costo debe estar entre 0 y 9999999999.99.',

            'visible.required' => 'El campo visible es obligatorio.',
            'visible.string' => 'El campo visible debe ser una cadena de texto.',
            'visible.max' => 'El campo visible no debe exceder los 5 caracteres.',
            'visible.in' => 'El campo visible debe ser true o false.',

            'image1.required' => 'La imagen principal es obligatoria.',
            'image1.image' => 'La imagen principal debe ser un archivo de imagen.',
            'image1.mimes' => 'La imagen principal debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image1.max' => 'La imagen principal no debe exceder los 2048 kilobytes.',
            'image2.image' => 'La imagen 2 debe ser un archivo de imagen.',
            'image2.mimes' => 'La imagen 2 debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image2.max' => 'La imagen 2 no debe exceder los 2048 kilobytes.',
            'image3.image' => 'La imagen 3 debe ser un archivo de imagen.',
            'image3.mimes' => 'La imagen 3 debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image3.max' => 'La imagen 3 no debe exceder los 2048 kilobytes.',
            'image4.image' => 'La imagen 4 debe ser un archivo de imagen.',
            'image4.mimes' => 'La imagen 4 debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image4.max' => 'La imagen 4 no debe exceder los 2048 kilobytes.',
            'image5.image' => 'La imagen 5 debe ser un archivo de imagen.',
            'image5.mimes' => 'La imagen 5 debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image5.max' => 'La imagen 5 no debe exceder los 2048 kilobytes.',  
        ];
     }
}
