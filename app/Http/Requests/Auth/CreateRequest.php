<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

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
        $passwordRule = Rules\Password::defaults()->mixedCase()->numbers()->min(8)->required();
        $numbers = Rules\Password::defaults()->numbers();
        $characters = Rules\Password::defaults()->symbols();
        return [
            'name' => 'required|min:3|max:45',
            'lastname' => 'required|min:3|max:45',
            'email' => 'required|email|min:5|max:100|unique:users',
            'gender' => 'required|min:4|max:6',
            'birthday' => 'required|date',
            'phone1' => 'required|min:10|numeric|unique:users',
            'phone2' => 'nullable|min:10|numeric',
            'address' => 'nullable|min:5|max:255|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => ['confirmed',$passwordRule,$numbers,$characters],//password_confirmation
            'cities_id' => 'required|numeric|exists:cities,id',
            'vikingo_roles_id' => 'nullable|numeric:exists:vikingo_roles,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no debe exceder los 45 caracteres.',

            'lastname.required' => 'El apellido es obligatorio.',
            'lastname.min' => 'El apellido debe tener al menos 3 caracteres.',
            'lastname.max' => 'El apellido no debe exceder los 45 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.min' => 'El correo electrónico debe tener al menos 5 caracteres.',
            'email.max' => 'El correo electrónico no debe exceder los 100 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixedCase' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un carácter especial.'
        ];
    }
}
