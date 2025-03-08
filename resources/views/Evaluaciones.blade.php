@extends('layouts.app')

@section('content')
<div class="container mt-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold">Evaluación</h2>
    </div>
    
    <div class="alert alert-warning">
        <strong>Advertencia:</strong> Asegúrese de ingresar los datos correctos para evitar errores.
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('evaluacion') }}">
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

    <div class="mb-4">
        <label for="evaluacion_general" class="form-label text-muted">Evaluación General (0% del 100%)</label>
        <input type="number" id="evaluacion_general" class="form-control" placeholder="Ingrese el porcentaje total" value="100">
    </div>

    <form action="{{ route('guardar.calificaciones') }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead class="bg-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th><input type="text" class="form-control text-center fw-bold" value="Nombre Evaluación" readonly></th>
                        <th><input type="text" class="form-control text-center fw-bold" value="Nombre Evaluación" readonly></th>
                        <th><input type="text" class="form-control text-center fw-bold" value="Nombre Evaluación" readonly></th>
                        <th><input type="text" class="form-control text-center fw-bold" value="Nombre Evaluación" readonly></th>
                        <th class="bg-success text-white">Calificación Total</th>
                    </tr>
                    <tr class="bg-light">
                        <th colspan="2" class="fw-bold text-muted">Porcentaje ()</th>
                        <th><input type="number" class="form-control text-center peso-ponderacion" value="25"></th>
                        <th><input type="number" class="form-control text-center peso-ponderacion" value="25"></th>
                        <th><input type="number" class="form-control text-center peso-ponderacion" value="25"></th>
                        <th><input type="number" class="form-control text-center peso-ponderacion" value="25"></th>
                        <th class="bg-white"></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($alumnos) && $alumnos->isNotEmpty())
                        @foreach ($alumnos as $index => $alumno)
                            <tr class="{{ $index % 2 == 0 ? 'bg-light' : 'bg-white' }} ">
                                <td class="fw-bold text-primary">{{ $index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="calificaciones[{{ $index }}][alumno_id]" value="{{ $alumno->id }}">
                                    <input type="text" class="form-control text-dark fw-bold" value="{{ $alumno->nombre_alumno }}" disabled>
                                </td>
                                <td><input type="number" class="form-control text-center ponderacion" name="calificaciones[{{ $index }}][ponderacion1]" value="{{ $alumno->ponderacion1 ?? '' }}" required></td>
                                <td><input type="number" class="form-control text-center ponderacion" name="calificaciones[{{ $index }}][ponderacion2]" value="{{ $alumno->ponderacion2 ?? '' }}" required></td>
                                <td><input type="number" class="form-control text-center ponderacion" name="calificaciones[{{ $index }}][ponderacion3]" value="{{ $alumno->ponderacion3 ?? '' }}" required></td>
                                <td><input type="number" class="form-control text-center ponderacion" name="calificaciones[{{ $index }}][ponderacion4]" value="{{ $alumno->ponderacion4 ?? '' }}" required></td>
                                <td><input type="number" class="form-control text-center fw-bold calificacion-total" name="calificaciones[{{ $index }}][calificacion]" value="{{ $alumno->calificacion ?? '' }}" readonly></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center text-danger">No hay alumnos registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('panel', ['grado' => $gradoFiltro ?? 'valor_default', 'grupo' => $grupoFiltro ?? 'valor_default']) }}" 
               class="btn btn-secondary me-3" 
               onclick="return confirmarRegresoPanel()">Regresar al Panel</a>
            <button type="submit" class="btn btn-success">Guardar</button>
        </div>
    </form>

</div>

<script>
    function confirmarRegresoPanel() {
        return confirm('⚠️ Advertencia: ¿Estás seguro de que deseas regresar al panel? Los cambios no guardados se perderán.');
    }

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