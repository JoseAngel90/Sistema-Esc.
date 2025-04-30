@extends('layouts.app')

@section('content')



<hr class="my-5">

    <h2 class="mb-4">Alumnos Registrados</h2>
    <!-- Botón para regresar al panel -->
    <div class="mb-3">
        <a href="{{ route('panel', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-secondary" id="regresarPanelBtn">Regresar al Panel</a>
    </div>

    <!-- Filtros de grado y grupo -->
    <div class="mb-4 d-flex">
        <input type="text" id="filterGrado" class="form-control me-2" placeholder="Filtrar por grado" value="{{ $grado }}" readonly>
        <input type="text" id="filterGrupo" class="form-control" placeholder="Filtrar por grupo" value="{{ $grupo }}" readonly>
    </div>

    <!-- Barra de búsqueda con botón -->
    <div class="mb-4 d-flex">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre del alumno" onkeyup="filterTable()">
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
                                <a href="{{ route('alumnos.create', ['alumnoEdit' => $alumno->id, 'grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-warning btn-sm">Editar</a>

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
            <div id="noResults" class="text-center text-danger" style="display: none;">No hay alumnos registrados en este grado y grupo.</div>
        </div>
    </div>
</div>


<div class="container my-5">
    <h1 class="mb-4">{{ isset($alumnoEdit) ? 'Editar Alumno' : 'Registrar Alumno' }}</h1>

    

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

    <!-- Advertencia -->
    <div class="alert alert-warning">
        <strong>Advertencia:</strong> Asegúrese de ingresar los datos correctos para evitar errores.
    </div>

    <!-- Formulario de registro o edición -->
    <form action="{{ isset($alumnoEdit) ? route('alumnos.update', $alumnoEdit->id) : route('alumnos.store') }}" method="POST">
    @csrf
    @if(isset($alumnoEdit))
        @method('PUT')
        <input type="hidden" name="id" value="{{ $alumnoEdit->id }}">
    @endif
        <div class="card p-4 shadow-sm">
            <div class="form-group">
                <label for="nombre_alumno" class="form-label">Nombre completo del Alumno</label>
                <input type="text" id="nombre_alumno" name="nombre_alumno" class="form-control" value="{{ old('nombre_alumno', $alumnoEdit->nombre_alumno ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="grado" class="form-label">Grado</label>
                <input type="text" id="grado" name="grado" class="form-control" value="{{ old('grado', $grado ?? $alumnoEdit->grado ?? '') }}" required maxlength="2" readonly>
            </div>

            <div class="form-group">
                <label for="grupo" class="form-label">Grupo</label>
                <input type="text" id="grupo" name="grupo" class="form-control" value="{{ old('grupo', $grupo ?? $alumnoEdit->grupo ?? '') }}" required maxlength="1" pattern="[A-Za-z]" title="Solo se permite una letra en mayúsculas" readonly>
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

    

<script>
    // Función para filtrar la tabla por grado, grupo y nombre
    function filterTable() {
        let filterGrado = document.getElementById('filterGrado').value.toUpperCase();
        let filterGrupo = document.getElementById('filterGrupo').value.toUpperCase();
        let filterNombre = document.getElementById('searchInput').value.toUpperCase();
        let table = document.getElementById('alumnosTable');
        let rows = table.getElementsByTagName('tr');
        let noResults = document.getElementById('noResults');
        let found = false;

        for (let i = 1; i < rows.length; i++) {
            let tdGrado = rows[i].getElementsByTagName('td')[1]; // Columna de Grado
            let tdGrupo = rows[i].getElementsByTagName('td')[2]; // Columna de Grupo
            let tdNombre = rows[i].getElementsByTagName('td')[0]; // Columna de Nombre
            if (tdGrado && tdGrupo && tdNombre) {
                let textGrado = tdGrado.textContent || tdGrado.innerText;
                let textGrupo = tdGrupo.textContent || tdGrupo.innerText;
                let textNombre = tdNombre.textContent || tdNombre.innerText;
                if (textGrado.toUpperCase().indexOf(filterGrado) > -1 &&
                    textGrupo.toUpperCase().indexOf(filterGrupo) > -1 &&
                    textNombre.toUpperCase().indexOf(filterNombre) > -1) {
                    rows[i].style.display = "";
                    found = true;
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        noResults.style.display = found ? "none" : "block";
    }

    // Ejecutar la función de filtrado al cargar la página
    document.addEventListener('DOMContentLoaded', filterTable);
</script>

@endsection
