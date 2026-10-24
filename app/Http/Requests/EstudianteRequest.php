<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstudianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $id = $this->route('estudiante')?->id;

        return [
            'ci'               => ['required', 'string', 'max:15', Rule::unique('estudiantes', 'ci')->ignore($id)],
            'nombres'          => ['required', 'string', 'max:80'],
            'apellidos'        => ['required', 'string', 'max:80'],
            'email'            => ['required', 'email', 'max:100', Rule::unique('estudiantes', 'email')->ignore($id)],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
        ];
    }
}