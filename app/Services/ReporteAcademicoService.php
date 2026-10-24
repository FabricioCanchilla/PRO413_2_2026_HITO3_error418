<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asignatura;
use App\Models\AsignacionDocente;

class ReporteAcademicoService implements ReporteAcademicoInterface
{
    public function asignacionesConDetalle(): array
    {
        // 1) Servidor institucional
        $asignaciones = AsignacionDocente::with(['docente', 'aula'])->get();

        // 2) Servidor Docker: una sola consulta, indexada por id (arreglo asociativo)
        $asignaturas = Asignatura::whereIn('id', $asignaciones->pluck('asignatura_id'))
            ->get()
            ->keyBy('id');

        // 3) Se combinan en la aplicación
        $reporte = [];
        foreach ($asignaciones as $a) {
            $reporte[] = [
                'asignatura' => $asignaturas[$a->asignatura_id]->nombre ?? 'No encontrada',
                'docente'    => $a->docente?->nombreCompleto() ?? '-',
                'aula'       => $a->aula?->nombre ?? '-',
                'horario'    => $a->horario ?? '-',
            ];
        }

        return $reporte;
    }
}