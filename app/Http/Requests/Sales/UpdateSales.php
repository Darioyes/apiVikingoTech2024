<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSales extends FormRequest
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
            'description'=>'required',
            'amount'=>'required|numeric|between:0,9999999999.99',
            'confirm_sale'=>'required|string|in:True,false',
            // 'shopping_cart'=>'required|string|in:True,false',
            'user_id'=>'required|integer|exists:users,id',
            'product_id'=>'required|integer|exists:products,id',
        ];
    }
}
