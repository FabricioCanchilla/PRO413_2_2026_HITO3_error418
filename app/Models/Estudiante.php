<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiantes';

    protected $fillable = ['ci', 'nombres', 'apellidos', 'email', 'fecha_nacimiento'];

    protected $casts = ['fecha_nacimiento' => 'date'];

    public function nombreCompleto(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }
}