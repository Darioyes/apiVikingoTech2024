<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarouselOrderRequest extends FormRequest
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
            'banners' => 'required|array|min:1',
            'banners.*.id' => 'required|integer|exists:carousels,id',
            'banners.*.order' => 'required|integer|min:0'
        ];
    }

    
    /**
     * Mensajes de error personalizados para las reglas de validación.
     */

    public function messages(): array
    {
        return [
            'banners.required' => 'El campo banners es requerido.',
            'banners.array' => 'El campo banners debe ser un array.',
            'banners.min' => 'Debe enviar al menos un banner.',
            'banners.*.id.required' => 'Cada banner debe tener un ID.',
            'banners.*.id.integer' => 'El ID del banner debe ser un número entero.',
            'banners.*.id.exists' => 'El ID del banner no existe en la base de datos.',
            'banners.*.order.required' => 'Cada banner debe tener un orden.',
            'banners.*.order.integer' => 'El orden debe ser un número entero.',
            'banners.*.order.min' => 'El orden no puede ser negativo.'
        ];
    }

       /**
     * Atributos personalizados para los campos de validación.
     */
    public function attributes(): array
    {
        return [
            'banners' => 'banners del carrusel',
            'banners.*.id' => 'ID del banner',
            'banners.*.order' => 'orden del banner'
        ];
    }
}
