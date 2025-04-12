<?php

namespace App\Http\Requests\Maintenances;

use Illuminate\Foundation\Http\FormRequest;

class CreateMaintenances extends FormRequest
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
            'cost_price'=> 'required|numeric|between:0,99999999.99',
            'delivery_date' => 'nullable|date',
            'image1' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image2' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image3' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image4' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'advance' => 'required|in:joined,in_progress,authorization,finalized',
            'repaired'=> 'nullable|string',
            'warranty'=> 'nullable|string',
            'users_id' => 'required|integer|exists:users,id',
        ];
    }
}
