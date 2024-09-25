<?php

namespace App\Http\Requests\CategoriesDirectCosts;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryDirect extends FormRequest
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
            'name' => 'required|string|max:100|unique:categories_direct_costs',
            'description' => 'required|string|max:500',
        ];
    }
}