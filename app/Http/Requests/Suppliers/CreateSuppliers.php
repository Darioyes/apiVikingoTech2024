<?php

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;

class CreateSuppliers extends FormRequest
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
            'name'=>'required|string|max:255',
            'nit'=>'nullable|string|max:20|unique:suppliers',
            'phone1' => 'required|string|max:15|unique:suppliers',
            'phone2'=>'nullable|string|max:15',
            'address'=>'required|string|max:255',
            'email'=>'required|email|unique:suppliers',
            'description'=>'nullable|string',
            'cities_id'=>'required|integer|exists:cities,id'
        ];
    }
}
