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
            'discount'=>'required|numeric|between:0,99999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'product_id' => 'required|integer|exists:products,id',
        ];
    }
}
