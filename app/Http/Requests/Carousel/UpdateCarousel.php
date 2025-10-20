<?php

namespace App\Http\Requests\Carousel;

use Illuminate\Foundation\Http\FormRequest;

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
            'order'=>'required|numeric|unique:carousels,order,'.$this->route('carousel')->id,
            'image' => 'nullable',
            //El rule::when hace que si se envia la imagen, aplique las reglas de validacion
            Rule::when(request()->hasFile('image'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
            Rule::when(!request()->has('keep_image') && !request()->hasFile('image'), ['prohibited']),
            'image2' => 'nullable',
            Rule::when(request()->hasFile('image2'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
            Rule::when(!request()->has('keep_image2') && !request()->hasFile('image2'), ['prohibited']),
            'image3' => 'nullable',
            Rule::when(request()->hasFile('image3'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
            Rule::when(!request()->has('keep_image3') && !request()->hasFile('image3'), ['prohibited']),
            'product_id' => 'required|integer|exists:products,id',
        ];
    }
}
