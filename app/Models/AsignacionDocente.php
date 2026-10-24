<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionDocente extends Model
{
    protected $connection = 'institucional';
    protected $table = 'asignacion_docentes';

    protected $fillable = ['docente_id', 'aula_id', 'gestion_id', 'asignatura_id', 'horario'];

    // Relaciones dentro del MISMO servidor: sí se pueden
    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }
}