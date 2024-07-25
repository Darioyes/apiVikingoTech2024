<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'name' => 'required|min:3|max:45',
            'lastname' => 'required|min:3|max:45',
            'email' => 'required|email|min:5|max:100|unique:users',
            'gender' => 'required|min:4|max:6',
            'birthday' => 'required|date',
            'phone1' => 'required|min:10|numeric|unique:users',
            'phone2' => 'nullable|min:10|max:12|numeric',
            'address' => 'nullable|min:5|max:255|string',
            'image' => 'nullable|min:5|max:255|string',
            'password' => 'required|min:1|max:12|confirmed',//password_confirmation
            'cities_id' => 'required|numeric|exists:cities,id',
            'vikingo_rol_id' => 'required|numeric:exists:vikingo_rols,id'
        ];
    }
}
