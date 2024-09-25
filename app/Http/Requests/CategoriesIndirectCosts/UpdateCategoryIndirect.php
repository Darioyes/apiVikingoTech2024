<?php

namespace App\Http\Requests\CategoriesIndirectCosts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryIndirect extends FormRequest
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
        $category = $this->route('categoriesindirectcost'); // Obtén el ID de la categoría que se está actualizando desde la ruta
        return [
            'name' => 'required|string|max:100|unique:categories_indirect_costs,name,' .$category,
            'description' => 'required|string|max:500',
        ];
    }
}
