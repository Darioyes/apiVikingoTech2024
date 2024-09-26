<?php

namespace App\Http\Requests\Cities;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCities extends FormRequest
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
        $city = $this->route('city'); // Obtén el ID de la ciudad que se está actualizando desde la ruta
        return [
            'city'=>'required|string|max:100|unique:cities,city,' .$city,
        ];
    }
}
