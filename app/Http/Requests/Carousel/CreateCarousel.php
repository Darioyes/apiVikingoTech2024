<?php

namespace App\Http\Requests\Carousel;

use Illuminate\Foundation\Http\FormRequest;

class CreateCarousel extends FormRequest
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
            'order'=>'required|numeric|unique:carousels,order',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image2' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            // 'image3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'product_id' => 'required|integer|exists:products,id',
        ];
    }

     public function messages(): array
    {
        return [
            'carousel.required' => 'El campo estado es obligatorio.',
            'carousel.in' => 'El campo estado debe ser activo o inactivo.',
            'order.required' => 'El campo orden es obligatorio.',
            'order.numeric' => 'El campo orden debe ser un número.',
            'order.unique' => 'El campo orden ya ha sido registrado.',
            'image.required' => 'La imagen 1 es obligatoria.',
            'image.image' => 'La imagen 1 debe ser un archivo de imagen.',
            'image.mimes' => 'La imagen 1 debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image.max' => 'La imagen 1 no debe ser mayor a 2MB.',
            'image2.required' => 'La imagen 2 es obligatoria.',
            'image2.image' => 'La imagen 2 debe ser un archivo de imagen.',
            'image2.mimes' => 'La imagen 2 debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image2.max' => 'La imagen 2 no debe ser mayor a 2MB.',
            'image3.image' => 'La imagen 3 debe ser un archivo de imagen.',
            'image3.mimes' => 'La imagen 3 debe ser un archivo de tipo: jpg, jpeg, png, webp.',
            'image3.max' => 'La imagen 3 no debe ser mayor a 2MB.',
            'product_id.required' => 'El campo producto es obligatorio.',
            'product_id.integer' => 'El campo producto debe ser un número entero.',
            'product_id.exists' => 'El producto seleccionado no existe.',
        ];
    }
}
