@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">{{ isset($alumnoEdit) ? 'Editar Alumno' : 'Registrar Alumno' }}</h1>

    <!-- Botón para regresar al home -->
    <div class="mb-3">
        <a href="{{ route('home') }}" class="btn btn-secondary">Regresar al Inicio</a>
    </div>

    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario de registro o edición -->
    <form action="{{ route('alumnos.store') }}" method="POST">
        @csrf
        @if(isset($alumnoEdit))
            @method('POST')
            <input type="hidden" name="id" value="{{ $alumnoEdit->id }}">
        @endif
        <div class="card p-4 shadow-sm">
            <div class="form-group">
                <label for="nombre_alumno" class="form-label">Nombre completo del Alumno</label>
                <input type="text" id="nombre_alumno" name="nombre_alumno" class="form-control" value="{{ old('nombre_alumno', $alumnoEdit->nombre_alumno ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="grado" class="form-label">Grado</label>
                <input type="text" id="grado" name="grado" class="form-control" value="{{ old('grado', $alumnoEdit->grado ?? '') }}" required maxlength="2" oninput="this.value = this.value.slice(0, 2).replace(/[^0-9]/g, '')">
            </div>

            <div class="form-group">
                <label for="grupo" class="form-label">Grupo</label>
                <input type="text" id="grupo" name="grupo" class="form-control" value="{{ old('grupo', $alumnoEdit->grupo ?? '') }}" required maxlength="1" pattern="[A-Za-z]" title="Solo se permite una letra en mayúsculas" oninput="this.value = this.value.toUpperCase()">
            </div>

            <!-- Selector para sexo -->
            <div class="form-group">
                <label for="hombre" class="form-label">Hombre</label>
                <input type="checkbox" id="hombre" name="hombre" value="1" class="form-check-input" {{ isset($alumnoEdit) && $alumnoEdit->hombre ? 'checked' : '' }}>
            </div>

            <div class="form-group">
                <label for="mujer" class="form-label">Mujer</label>
                <input type="checkbox" id="mujer" name="mujer" value="1" class="form-check-input" {{ isset($alumnoEdit) && $alumnoEdit->mujer ? 'checked' : '' }}>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary w-100">{{ isset($alumnoEdit) ? 'Actualizar' : 'Registrar' }}</button>
            </div>
        </div>
    </form>

    <hr class="my-5">

    <h2 class="mb-4">Alumnos Registrados</h2>

    <!-- Barra de búsqueda con botón -->
    <div class="mb-4 d-flex">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre del alumno" onkeyup="searchTable()">
    </div>

    <!-- Mostrar lista de alumnos registrados -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="alumnosTable">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Grado</th>
                        <th>Grupo</th>
                        <th>Género</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alumnos as $alumno)
                        <tr>
                            <td>{{ $alumno->nombre_alumno }}</td>
                            <td>{{ $alumno->grado }}</td>
                            <td>{{ $alumno->grupo }}</td>
                            <td>{{ $alumno->hombre ? 'Hombre' : 'Mujer' }}</td>
                            <td>
                                <!-- Enlace para editar -->
                                <a href="{{ route('alumnos.create', $alumno->id) }}" class="btn btn-warning btn-sm">Editar</a>

                                <!-- Formulario para eliminar -->
                                <form action="{{ route('alumnos.destroy', $alumno->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este alumno?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Lista vacía</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Función para filtrar la tabla
    function searchTable() {
        let input = document.getElementById('searchInput');
        let filter = input.value.toUpperCase();
        let table = document.getElementById('alumnosTable');
        let rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            let td = rows[i].getElementsByTagName('td')[0]; // Buscamos en la primera columna (Nombre)
            if (td) {
                let textValue = td.textContent || td.innerText;
                if (textValue.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
</script>

@endsection
