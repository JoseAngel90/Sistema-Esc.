@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pase de Lista</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-sm rounded-3 p-4">
            <h1 class="text-center mb-4 text-primary">Pase de Lista</h1>

             <!-- Advertencia -->
    <div class="alert alert-warning">
        <strong>Advertencia:</strong> Asegúrese de ingresar los datos correctos para evitar errores.
    </div>

            <!-- Filtros y búsqueda -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="w-50">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar alumno" onkeyup="searchTable()">
                </div>
                <div class="d-flex w-50 justify-content-end">
                    <div class="form-group mx-2 w-50">
                        <select id="gradoFilter" class="form-select" onchange="filterByGroupAndGrade()" disabled>
                            <option value="">Seleccionar Grado</option>
                            @foreach($alumnos->pluck('grado')->unique() as $grado)
                                <option value="{{ $grado }}">{{ $grado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mx-2 w-50">
                        <select id="groupFilter" class="form-select" onchange="filterByGroupAndGrade()" disabled>
                            <option value="">Seleccionar Grupo</option>
                            @foreach($alumnos->pluck('grupo')->unique() as $grupo)
                                <option value="{{ $grupo }}">{{ $grupo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('pase.lista.store') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Nombre del Alumno</th>
                                <th>Grado</th>
                                <th>Grupo</th>
                                <th>Fecha</th>
                                <th>Asistencia</th>
                                <th>Justificante</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody id="alumnosTable">
                            @forelse ($alumnos as $alumno)
                                <tr class="alumno-row" data-grupo="{{ $alumno->grupo }}" data-grado="{{ $alumno->grado }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $alumno->nombre_alumno }}</td>
                                    <td>{{ $alumno->grado }}</td>
                                    <td>{{ $alumno->grupo }}</td>
                                    <td>
                                        <input type="date" name="fecha[{{ $alumno->id }}]" class="form-control" value="{{ now()->format('Y-m-d') }}" readonly>
                                    </td>
                                    <td>
                                        <select name="asistencia[{{ $alumno->id }}]" class="form-select">
                                            <option value="presente" 
                                                {{ isset($asistencias->where('alumno_id', $alumno->id)->first()->asistencia) && $asistencias->where('alumno_id', $alumno->id)->first()->asistencia == 'presente' ? 'selected' : '' }}>
                                                Presente
                                            </option>
                                            <option value="ausente" 
                                                {{ isset($asistencias->where('alumno_id', $alumno->id)->first()->asistencia) && $asistencias->where('alumno_id', $alumno->id)->first()->asistencia == 'ausente' ? 'selected' : '' }}>
                                                Ausente
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="toggleDateEditability({{ $alumno->id }})">Justificante</button>
                                    </td>
                                    <td>
                                        @php
                                             $asistencia = $asistencias->where('alumno_id', $alumno->id)->first();
                                        @endphp

                                        <div class="badge 
                                            @if($asistencia && $asistencia->asistencia == 'presente') 
                                                bg-success 
                                            @elseif($asistencia && $asistencia->asistencia == 'ausente') 
                                                bg-danger 
                                            @else 
                                                bg-secondary 
                                            @endif">
                                            {{ $asistencia ? ucfirst($asistencia->asistencia) : 'Sin Registro' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-danger">Lista vacía</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="noResults" class="text-center text-danger" style="display: none;">No hay alumnos registrados en este grado y grupo.
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success">Guardar Asistencia</button>
                    <a href="{{ route('panel', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-secondary" onclick="return confirm('¿Está seguro de que desea regresar al panel? Asegúrese de haber guardado todos los cambios.')">Regresar al Panel</a>
                    </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById("alumnosTable");
            const rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                const td = rows[i].getElementsByTagName("td")[1]; // La columna de nombre del alumno
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
            checkNoResults();
        }

        function filterByGroupAndGrade() {
            const selectedGroup = document.getElementById('groupFilter').value;
            const selectedGrade = document.getElementById('gradoFilter').value;
            const rows = document.querySelectorAll('.alumno-row');

            rows.forEach(row => {
                const alumnoGroup = row.getAttribute('data-grupo');
                const alumnoGrade = row.getAttribute('data-grado');

                if ((selectedGroup === "" || alumnoGroup === selectedGroup) &&
                    (selectedGrade === "" || alumnoGrade === selectedGrade)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
            checkNoResults();
        }

        function checkNoResults() {
            const rows = document.querySelectorAll('.alumno-row');
            let visibleRows = 0;

            rows.forEach(row => {
                if (row.style.display !== "none") {
                    visibleRows++;
                }
            });

            const noResults = document.getElementById('noResults');
            noResults.style.display = visibleRows === 0 ? "block" : "none";
        }

        function toggleDateEditability(alumnoId) {
            const fechaInput = document.querySelector(`input[name="fecha[${alumnoId}]"]`);
            if (fechaInput) {
                fechaInput.removeAttribute('readonly');
                fechaInput.focus();
            }
        }

        // Ejecutar la función de filtrado al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            const gradoFilter = document.getElementById('gradoFilter');
            const groupFilter = document.getElementById('groupFilter');

            // Seleccionar automáticamente el primer valor de los filtros
            if (gradoFilter.options.length > 1) {
                gradoFilter.selectedIndex = 1;
                gradoFilter.disabled = true;
            }
            if (groupFilter.options.length > 1) {
                groupFilter.selectedIndex = 1;
                groupFilter.disabled = true;
            }

            filterByGroupAndGrade();
        });
    </script>
</body>
</html>
@endsection