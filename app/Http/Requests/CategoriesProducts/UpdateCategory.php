<?php

namespace App\Http\Requests\CategoriesProducts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategory extends FormRequest
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
        $category = $this->route('categoriesproduct'); // Obtén el ID de la categoría que se está actualizando desde la ruta
        //dd($category);

        return [
            'name' => 'required|string|max:100|unique:categories_products,name,' .$category,
            'description' => 'required|string|max:500',
        ];
    }
}
