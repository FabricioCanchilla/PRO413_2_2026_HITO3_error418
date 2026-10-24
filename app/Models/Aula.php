<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $connection = 'institucional';
    protected $table = 'aulas';

    protected $fillable = ['nombre', 'capacidad'];
}