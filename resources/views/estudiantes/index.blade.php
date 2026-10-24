<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Estudiantes</title></head>
<body>
<h1>Estudiantes</h1>
@if (session('ok')) <p style="color:green">{{ session('ok') }}</p> @endif
@if (session('error')) <p style="color:red">{{ session('error') }}</p> @endif
<a href="{{ route('estudiantes.create') }}">Nuevo estudiante</a>
<table border="1" cellpadding="6">
  <tr><th>CI</th><th>Nombre</th><th>Email</th><th>Edad</th><th>Acciones</th></tr>
  @forelse ($estudiantes as $ci => $e)
    <tr>
      <td>{{ $ci }}</td><td>{{ $e['nombre'] }}</td><td>{{ $e['email'] }}</td><td>{{ $e['edad'] ?? '-' }}</td>
      <td>
        <a href="{{ route('estudiantes.edit', $e['id']) }}">Editar</a>
        <form method="POST" action="{{ route('estudiantes.destroy', $e['id']) }}" style="display:inline">
          @csrf @method('DELETE')
          <button onclick="return confirm('¿Eliminar?')">Eliminar</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="5">Sin registros</td></tr>
  @endforelse
</table>
</body>
</html>