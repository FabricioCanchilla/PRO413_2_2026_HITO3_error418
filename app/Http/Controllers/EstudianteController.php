<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EstudianteRequest;
use App\Models\Estudiante;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EstudianteController extends Controller
{
    public function index(): View
    {
        // Estructura de datos: arreglo asociativo indexado por CI
        $estudiantes = [];
        foreach (Estudiante::orderBy('apellidos')->get() as $e) {
            $estudiantes[$e->ci] = [
                'id'     => $e->id,
                'nombre' => $e->nombreCompleto(),
                'email'  => $e->email,
                'edad'   => $e->fecha_nacimiento?->age,
            ];
        }

        return view('estudiantes.index', ['estudiantes' => $estudiantes]);
    }

    public function create(): View
    {
        return view('estudiantes.form', ['estudiante' => new Estudiante()]);
    }

    public function store(EstudianteRequest $request): RedirectResponse
    {
        return $this->guardar(fn () => Estudiante::create($request->validated()), 'Estudiante registrado.');
    }

    public function edit(Estudiante $estudiante): View
    {
        return view('estudiantes.form', ['estudiante' => $estudiante]);
    }

    public function update(EstudianteRequest $request, Estudiante $estudiante): RedirectResponse
    {
        return $this->guardar(fn () => $estudiante->update($request->validated()), 'Estudiante actualizado.');
    }

    public function destroy(Estudiante $estudiante): RedirectResponse
    {
        return $this->guardar(fn () => $estudiante->delete(), 'Estudiante eliminado.');
    }

    private function guardar(callable $accion, string $mensaje): RedirectResponse
    {
        try {
            $accion();
        } catch (QueryException $e) {
            Log::error('Error BD estudiantes', ['detalle' => $e->getMessage()]);
            return back()->withInput()->with('error', 'No se pudo completar la operación. Intenta de nuevo.');
        }

        return redirect()->route('estudiantes.index')->with('ok', $mensaje);
    }
}