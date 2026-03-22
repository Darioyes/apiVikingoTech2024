<?php

namespace App\Http\Requests\ShoopingCart;

use Illuminate\Foundation\Http\FormRequest;

class CreateShoopingCart extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El campo user_id es obligatorio.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'product_id.required' => 'El campo product_id es obligatorio.',
            'product_id.exists' => 'El producto especificado no existe.',
            'quantity.required' => 'El campo quantity es obligatorio.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad debe ser al menos 1.'
        ];
    }
}
