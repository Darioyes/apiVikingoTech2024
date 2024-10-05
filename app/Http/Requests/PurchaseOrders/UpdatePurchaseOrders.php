<?php

namespace App\Http\Requests\PurchaseOrders;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrders extends FormRequest
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
            'purcharse'=>'required|numeric |between:0,9999999999.99',
            'amount'=>'required|numeric |between:0,9999999999.99',
            'description'=>'required|string',
            'purcharse_order'=>'required|string|max:255',
            'products_id'=>'required|exists:products,id',
            'suppliers_id'=>'required|exists:suppliers,id',
        ];
    }
}
