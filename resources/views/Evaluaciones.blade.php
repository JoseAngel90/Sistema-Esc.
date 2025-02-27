@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Mostrar mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center fw-bold" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-center flex-grow-1 text-primary fw-bold">Formato de Evaluación Diagnóstica</h2>
        
    </div>

    <div class="mb-4">
        <label for="evaluacion_general" class="form-label fw-bold text-secondary">Evaluación General (% del 100%):</label>
        <input type="number" id="evaluacion_general" class="form-control border-primary rounded-3 shadow-sm" placeholder="Ingrese el porcentaje total" value="100">
    </div>

    <form action="{{ route('guardar.calificaciones') }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center">
                <thead class="bg-primary text-white">
                    <tr>
                        <th></th>
                        <th></th>
                        <th><input type="text" class="form-control fw-bold text-center ponderacion-nombre" value="Ponderación 1"></th>
                        <th><input type="text" class="form-control fw-bold text-center ponderacion-nombre" value="Ponderación 2"></th>
                        <th><input type="text" class="form-control fw-bold text-center ponderacion-nombre" value="Ponderación 3"></th>
                        <th><input type="text" class="form-control fw-bold text-center ponderacion-nombre" value="Ponderación 4"></th>
                        <th class="bg-success">Calificación Total</th>
                    </tr>
                    <tr class="bg-light">
                        <th colspan="2" class="fw-bold text-secondary">Ponderación (%)</th>
                        <th><input type="number" class="form-control peso-ponderacion border-info text-center" value="25"></th>
                        <th><input type="number" class="form-control peso-ponderacion border-info text-center" value="25"></th>
                        <th><input type="number" class="form-control peso-ponderacion border-info text-center" value="25"></th>
                        <th><input type="number" class="form-control peso-ponderacion border-info text-center" value="25"></th>
                        <th class="bg-white"></th>
                    </tr>
                </thead>

                <tbody>
                    @if(isset($alumnos) && $alumnos->isNotEmpty())
                        @foreach ($alumnos as $index => $alumno)
                            <tr class="{{ $index % 2 == 0 ? 'table-light' : 'table-white' }}">
                                <td class="fw-bold text-primary">{{ $index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="calificaciones[{{ $index }}][alumno_id]" value="{{ $alumno->id }}">
                                    <input type="text" class="form-control border-secondary text-dark fw-bold text-center" 
                                        value="{{ $alumno->nombre_alumno }}" disabled>
                                </td>
                                <td><input type="number" class="form-control ponderacion border-info text-center" name="ponderacion1[]" value="{{ $alumno->ponderacion1 ?? '' }}" required></td>
                                <td><input type="number" class="form-control ponderacion border-info text-center" name="ponderacion2[]" value="{{ $alumno->ponderacion2 ?? '' }}" required></td>
                                <td><input type="number" class="form-control ponderacion border-info text-center" name="ponderacion3[]" value="{{ $alumno->ponderacion3 ?? '' }}" required></td>
                                <td><input type="number" class="form-control ponderacion border-info text-center" name="ponderacion4[]" value="{{ $alumno->ponderacion4 ?? '' }}" required></td>
                                <td><input type="number" class="form-control calificacion-total border-success text-center fw-bold" name="calificaciones[{{ $index }}][calificacion]" value="{{ $alumno->calificacion ?? '' }}" readonly></td>
                            </tr>
                        @endforeach
                    @else

                        <tr>
                            <td colspan="7" class="text-center fw-bold text-danger">No hay alumnos registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-end">
    <a href="{{ url('home') }}" class="btn btn-outline-primary fw-bold me-3">Regresar</a>
    <button type="submit" class="btn btn-success fw-bold shadow-lg px-4">Guardar</button>
</div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let evaluacionGeneralInput = document.getElementById('evaluacion_general');
    let pesos = document.querySelectorAll('.peso-ponderacion');

    function actualizarCalificaciones() {
        let evaluacionGeneral = parseFloat(evaluacionGeneralInput.value) || 100;

        document.querySelectorAll('.ponderacion').forEach(input => {
            input.addEventListener('input', function() {
                let row = this.closest('tr');
                let ponderaciones = row.querySelectorAll('.ponderacion');
                let ponderacionesPesos = document.querySelectorAll('.peso-ponderacion');
                let total = 0, pesoTotal = 0;

                ponderaciones.forEach((p, index) => {
                    let valor = parseFloat(p.value) || 0;
                    let peso = parseFloat(ponderacionesPesos[index].value) || 0;
                    total += valor * (peso / 100);
                    pesoTotal += peso;
                });

                let calificacionTotal = (total / pesoTotal) * evaluacionGeneral;
                row.querySelector('.calificacion-total').value = calificacionTotal.toFixed(2);
            });
        });
    }

    evaluacionGeneralInput.addEventListener('input', actualizarCalificaciones);
    pesos.forEach(input => input.addEventListener('input', actualizarCalificaciones));
    actualizarCalificaciones();
});
</script>

@endsection
