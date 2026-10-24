<?php

declare(strict_types=1);

use App\Http\Controllers\EstudianteController;
use App\Services\ReporteAcademicoInterface;
use App\Services\ReporteAcademicoService;
use Illuminate\Support\Facades\Route;

app()->bind(ReporteAcademicoInterface::class, ReporteAcademicoService::class);

Route::redirect('/', '/estudiantes');
Route::resource('estudiantes', EstudianteController::class)->except(['show']);

Route::get('/reporte', function (ReporteAcademicoInterface $servicio) {
    try {
        return response()->json($servicio->asignacionesConDetalle());
    } catch (\Throwable $e) {
        report($e);
        return response()->json(['error' => 'No se pudo consultar el servidor institucional.'], 503);
    }
});