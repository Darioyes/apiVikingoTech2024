<?php

namespace App\Http\Requests\Carousel;



use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarousel extends FormRequest
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
            'carousel'=>'required|in:active,inactive',
            'order'=>'required|integer|unique:carousels,order,'.$this->route('carousel'),
            'image' => 'nullable',
            //El rule::when hace que si se envia la imagen, aplique las reglas de validacion
            Rule::when(request()->hasFile('image'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
            Rule::when(!request()->has('keep_image') && !request()->hasFile('image'), ['prohibited']),
            'image2' => 'nullable',
            Rule::when(request()->hasFile('image2'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
            Rule::when(!request()->has('keep_image2') && !request()->hasFile('image2'), ['prohibited']),
            // 'image3' => 'nullable',
            // Rule::when(request()->hasFile('image3'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
            // Rule::when(!request()->has('keep_image3') && !request()->hasFile('image3'), ['prohibited']),
            'product_id' => 'required|integer|exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'carousel.required' => 'El estado del carrusel es requerido.',
            'carousel.in' => 'El estado del carrusel debe ser activo o inactivo.',
            'order.required' => 'El orden del carrusel es requerido.',
            'order.numeric' => 'El orden del carrusel debe ser un número.',
            'order.unique' => 'El orden del carrusel ya está en uso.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image.max' => 'La imagen no debe superar los 2MB.',
            'image2.image' => 'El archivo debe ser una imagen válida.',
            'image2.mimes' => 'La imagen debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image2.max' => 'La imagen no debe superar los 2MB.',
            'image3.image' => 'El archivo debe ser una imagen válida.',
            'image3.mimes' => 'La imagen debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image3.max' => 'La imagen no debe superar los 2MB.',
            'product_id.required' => 'El ID del producto es requerido.',
            'product_id.integer' => 'El ID del producto debe ser un número entero.',
            'product_id.exists' => 'El ID del producto no existe en la base de datos.'
        ];
    }
}
