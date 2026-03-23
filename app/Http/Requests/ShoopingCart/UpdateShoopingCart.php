<?php

namespace App\Http\Requests\ShoopingCart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShoopingCart extends FormRequest
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
            'user_id' => 'sometimes|exists:users,id',
            'product_id' => 'sometimes|exists:products,id',
            'amount' => 'sometimes|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'El usuario especificado no existe.',
            'product_id.exists' => 'El producto especificado no existe.',
            'amount.integer' => 'La cantidad debe ser un número entero.',
            'amount.min' => 'La cantidad debe ser al menos 1.'
        ];
    }
}
