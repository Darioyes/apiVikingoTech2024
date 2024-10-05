<?php

namespace App\Http\Requests\DirectCosts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDirectCosts extends FormRequest
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
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'amount' => 'required|numeric|between:0,99999999.99',
            'price' => 'required|numeric|between:0,99999999.99',
            'categories_direct_costs_id' => 'required|integer|exists:categories_direct_costs,id'
        ];
    }
}
