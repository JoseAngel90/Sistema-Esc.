@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pase de Lista</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        .table-container {
            margin-top: 20px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .form-select {
            width: 100%;
        }

        .alert {
            margin-top: 20px;
        }

        .header {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 25px;
        }

        .indicator {
            font-size: 0.9rem;
            color: white;
            padding: 5px;
            border-radius: 5px;
        }

        .presente {
            background-color: #28a745;
        }

        .ausente {
            background-color: #dc3545;
        }

        .empty-list {
            text-align: center;
            font-size: 1.2rem;
            color: #dc3545;
            margin-top: 20px;
        }

        .btn-justificante {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-justificante:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .filters-container {
            margin-top: 20px;
        }

        .form-group {
            margin-right: 15px;
        }

        .search-container {
            max-width: 500px;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="card p-4">
            <h1 class="text-center mb-4 header">Pase de Lista</h1>

            <!-- Filtros y búsqueda -->
            <div class="filters-container d-flex justify-content-between align-items-center mb-4">
                <div class="search-container">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar alumno" onkeyup="searchTable()">
                </div>
                <div class="d-flex">
                    <div class="form-group mx-2">
                        <select id="gradoFilter" class="form-select" onchange="filterByGroupAndGrade()">
                            <option value="">Seleccionar Grado</option>
                            @foreach($alumnos->pluck('grado')->unique() as $grado)
                                <option value="{{ $grado }}">{{ $grado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mx-2">
                        <select id="groupFilter" class="form-select" onchange="filterByGroupAndGrade()">
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
                <div class="table-container">
                    <table class="table table-bordered table-hover table-striped">
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
                                        <input type="date" name="fecha[{{ $alumno->id }}]" class="form-control fecha" value="{{ now()->format('Y-m-d') }}" readonly>
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
                                        <button type="button" class="btn btn-justificante" onclick="toggleDateEditability({{ $alumno->id }})">Justificante</button>
                                    </td>
                                    <td>
                                    @php
                                         $asistencia = $asistencias->where('alumno_id', $alumno->id)->first();
                                    @endphp

                                    <div class="indicator mt-2 
                                        @if($asistencia && $asistencia->asistencia == 'presente') 
                                            presente 
                                            @elseif($asistencia && $asistencia->asistencia == 'ausente') 
                                                ausente 
                                            @else 
                                                ausente 
                                            @endif">
                                            {{ $asistencia ? ucfirst($asistencia->asistencia) : 'Sin Registro' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-list">Lista vacía</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-success">Guardar Asistencia</button>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Regresar al Inicio</a>
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
        }

        function toggleDateEditability(alumnoId) {
            const fechaInput = document.querySelector(`input[name="fecha[${alumnoId}]"]`);
            if (fechaInput) {
                fechaInput.removeAttribute('readonly');
                fechaInput.focus();
            }
        }
    </script>
</body>
</html>
@endsection
