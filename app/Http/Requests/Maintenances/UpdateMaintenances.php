<?php

namespace App\Http\Requests\Maintenances;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaintenances extends FormRequest
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
            'product' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'reference' => 'nullable|string|max:100',
            'price' => 'required|numeric|between:0,99999999.99',
            'delivery_date' => 'nullable|date',
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
            'advance' => 'required|in:joined,in_progress,authorization,finalized',
            'repaired'=> 'nullable|string',
            'warranty'=> 'nullable|string',
            'users_id' => 'required|integer|exists:users,id',
        ];
    }
}
