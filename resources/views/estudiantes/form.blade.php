<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Estudiante</title></head>
<body>
<h1>{{ $estudiante->exists ? 'Editar' : 'Nuevo' }} estudiante</h1>
@if (session('error')) <p style="color:red">{{ session('error') }}</p> @endif
@if ($errors->any())
  <ul style="color:red">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
@endif
<form method="POST" action="{{ $estudiante->exists ? route('estudiantes.update', $estudiante) : route('estudiantes.store') }}">
  @csrf
  @if ($estudiante->exists) @method('PUT') @endif
  <p>CI: <input name="ci" value="{{ old('ci', $estudiante->ci) }}"></p>
  <p>Nombres: <input name="nombres" value="{{ old('nombres', $estudiante->nombres) }}"></p>
  <p>Apellidos: <input name="apellidos" value="{{ old('apellidos', $estudiante->apellidos) }}"></p>
  <p>Email: <input name="email" value="{{ old('email', $estudiante->email) }}"></p>
  <p>Fecha nac.: <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $estudiante->fecha_nacimiento?->format('Y-m-d')) }}"></p>
  <button>Guardar</button> <a href="{{ route('estudiantes.index') }}">Cancelar</a>
</form>
</body>
</html>