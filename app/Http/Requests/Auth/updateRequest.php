<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class updateRequest extends FormRequest
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
        $userId = $this->route('user'); // Obtén el ID del usuario que se está actualizando desde la ruta
        //dd($userId);
        return [
            'name' => 'required|min:3|max:45',
            'lastname' => 'required|min:3|max:45',
            'gender' => 'required|min:4|max:6',
            'birthday' => 'required|date',
            'phone1' => 'required|min:10|numeric|unique:users,phone1,' . $userId->id,
            'phone2' => 'nullable|min:10|numeric',
            'address' => 'nullable|min:5|max:255|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cities_id' => 'required|numeric|exists:cities,id',
            'vikingo_roles_id' => 'required|numeric|exists:vikingo_roles,id'
        ];
    }
}
