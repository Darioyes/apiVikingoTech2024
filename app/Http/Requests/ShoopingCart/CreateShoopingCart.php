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
            'amount' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El ID usuario es obligatorio.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'product_id.required' => 'El ID producto es obligatorio.',
            'product_id.exists' => 'El producto especificado no existe.',
            'amount.required' => 'El campo amount es obligatorio.',
            'amount.integer' => 'La cantidad debe ser un número entero.',
            'amount.min' => 'La cantidad debe ser al menos 1.'
        ];
    }
}
