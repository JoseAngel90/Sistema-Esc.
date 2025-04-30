@extends('layouts.app')

@section('content')
<div class="container mt-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold">Examen Diagnóstico</h2>
    </div>
    
    <div class="alert alert-warning">
        <strong>Advertencia:</strong> Asegúrese de ingresar los datos correctos para evitar errores.
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('diagnostico.mostrar') }}">
            <div class="row">
                <div class="col-md-6">
                    <label for="grado" class="form-label">Grado</label>
                    <input type="text" id="grado" name="grado" class="form-control" value="{{ request('grado') }}" readonly>
                </div>
                <div class="col-md-6">
                    <label for="grupo" class="form-label">Grupo</label>
                    <input type="text" id="grupo" name="grupo" class="form-control" value="{{ request('grupo') }}" readonly>
                </div>
            </div>
        </form>
    </div>

    

    <form id="form-reactivos" action="{{ route('guardar.reactivos') }}" method="POST">
    @csrf

    <!-- Campo para ingresar el número máximo de reactivos -->
<div class="mb-4">
    <label for="reactivos_max" class="form-label">Número máximo de reactivos</label>
    <input type="number" id="reactivos_max" name="reactivos_max" class="form-control" 
           required min="1" 
           value="{{ old('reactivos_max') }}" 
           oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')">
</div>


    <!-- Botón para aceptar los reactivos máximos -->
    <div class="mb-4">
        <button type="button" class="btn btn-primary" id="aceptar-reactivos" onclick="aceptarReactivos()">Aceptar Reactivos</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Nombre del Alumno</th>
                    <th>Reactivos Contestados</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($alumnos) && $alumnos->isNotEmpty())
                    @foreach ($alumnos as $index => $alumno)
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-white' }}">
                            <td class="fw-bold text-primary">{{ $index + 1 }}</td>
                            <td>
                                <input type="hidden" name="reactivos[{{ $index }}][alumno_id]" value="{{ $alumno->id }}">
                                <input type="text" class="form-control text-dark fw-bold" value="{{ $alumno->nombre_alumno }}" disabled>
                            </td>
                            <td>
                            <input type="number" class="form-control text-center reactivo-input"
                                name="reactivos[{{ $index }}][contestados]"
                                value="{{ old('reactivos')[$index]['contestados'] ?? $alumno->contestados ?? '' }}"
                                required min="1"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')">
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center text-danger">No hay alumnos registrados.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('panel', ['grado' => $grado ?? 'valor_default', 'grupo' => $grupo ?? 'valor_default']) }}" 
           class="btn btn-secondary me-3" 
           onclick="return confirmarRegresoPanel()">Regresar al Panel</a>
        <button type="submit" class="btn btn-success" id="guardar-reactivos">Guardar</button>
    </div>
</form>


<div class="mt-5">
    <h4 class="text-center text-primary fw-bold">Promedios de los Alumnos</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Nombre del Alumno</th>
                    <th>Promedio</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($alumnos) && $alumnos->isNotEmpty())
                    @foreach ($alumnos as $index => $alumno)
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-white' }}">
                            <td class="fw-bold text-primary">{{ $index + 1 }}</td>
                            <td>{{ $alumno->nombre_alumno }}</td>
                            <td>
                                {{ $alumno->promedio !== null ? number_format($alumno->promedio, 2) : 'No disponible' }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center text-danger">No hay alumnos registrados.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>


</div>



@endsection
