<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $connection = 'institucional';
    protected $table = 'docentes';

    protected $fillable = ['ci', 'nombres', 'apellidos', 'email'];

    public function nombreCompleto(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }
}   