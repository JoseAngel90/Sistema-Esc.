<!-- Modal para ingresar el periodo -->
<div class="modal fade" id="modalPeriodo" tabindex="-1" aria-labelledby="modalPeriodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPeriodoLabel">Ingresa el periodo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <label for="periodo">Periodo:</label>
                <input type="text" class="form-control" id="periodo" name="periodo" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-periodo">Guardar</button>
            </div>
        </div>
    </div>
</div>



@extends('layouts.app')

@section('content')

<!-- Botón para regresar a Evaluación -->
<a href="{{ route('panel', ['grado' => $gradoFiltro, 'grupo' => $grupoFiltro]) }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left-circle"></i> Regresar al Panel
</a>



<!-- Formulario para guardar ponderaciones -->
<form id="formRubro" action="{{ route('guardar.rubro') }}" method="POST">
    @csrf
    <input type="hidden" name="alumno_id" value="{{ auth()->user()->alumno_id }}">

    @php
    $tipos = ['apoyo_p', 'proyectos', 'trabajos_clase', 'tareas', 'examen'];
    @endphp

<div class="row mb-4">
    @foreach ($tipos as $index => $tipo)
        <div class="col-md-2 text-center">
            <label class="fw-bold" for="peso-Rubro{{ $index + 1 }}">Criterio{{ $index + 1 }}</label>
            <input type="number" class="form-control peso-tabla"
                   id="peso-Rubro{{ $index + 1 }}"
                   name="Rubro{{ $index + 1 }}"
                   data-tipo="{{ $tipo }}"
                   value="{{ old('Rubro' . ($index + 1), $registro->{'rubro' . ($index + 1)} ?? '') }}"
                   min="0" step="0.01">
        </div>
    @endforeach
</div>

    <div class="card mt-4">
        <div class="card-body text-center">
            <h5 class="mb-3">Total Ponderación: 
                <span id="totalPonderacion" style="font-weight: bold;">0.00</span>
            </h5>
            <div id="alertaTotal" class="text-danger" style="display: none;">
                La suma de los rubros no puede ser mayor a 100. Por favor, ajusta los valores.
            </div>
            <button type="submit" id="btnGuardar" class="btn btn-primary mt-3">Guardar Ponderación</button>
        </div>
    </div>
</form>
<br>
    <div class="alert alert-warning">
        <strong>Advertencia:</strong> Asegúrese de ingresar los datos correctos para evitar errores.
    </div>

<!-- Pestañas -->
<ul class="nav nav-tabs" id="calificacionesTabs" role="tablist">
    @foreach (["apoyo_p", "proyectos", "trabajos_clase", "tareas", "examen"] as $index => $tipo)
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tipo }}-tab" data-bs-toggle="tab" 
               href="#{{ $tipo }}" role="tab" aria-controls="{{ $tipo }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                Criterio{{ $index + 1 }}
            </a>
        </li>
    @endforeach
</ul>


<!-- Contenido de pestañas -->
<div class="tab-content" id="calificacionesTabsContent">
    @foreach (["apoyo_p", "proyectos", "trabajos_clase", "tareas", "examen"] as $tipo)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tipo }}" role="tabpanel" 
             aria-labelledby="{{ $tipo }}-tab">
             
            <form action="{{ route('calificaciones.guardar') }}" method="POST">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" name="tipo_pestania" value="{{ $tipo }}">

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover shadow-sm">
                        <thead class="table-primary text-center">
                            <tr>
                                <th><i class="bi bi-person-fill"></i> Alumno</th>

                                <!-- Generar las columnas de las evaluaciones por pestaña -->
                                @foreach (range(1, ($tipo == "apoyo_p") ? 5 : 3) as $i)
                                    <th>
                                        <i class="bi bi-check-circle"></i>
                                        <span class="editable-title" contenteditable="true" data-index="{{ $i }}" data-tipo="{{ $tipo }}">

                                            Evaluación {{ $i }}
                                        </span>

                                        @if ($tipo != "apoyo_p")
                                            <!-- Input de Total de elementos correctos debajo de la evaluación -->
                                            <input type="number" class="form-control mt-1" 
                                                   id="elementos_correctos_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}" 
                                                   placeholder="Total de elementos correctos" 
                                                   value="{{ $calificacionesPorPestania[$tipo][$alumno->id]->{'valor_maximo' . $i} ?? '' }}" 
                                                   data-correctos="{{ $calificacionesPorPestania[$tipo][$alumno->id]->{'valor_maximo' . $i} ?? '' }}" 
                                                   oninput="calcularCalificacion('{{ $alumno->id }}', {{ $i }}, '{{ $tipo }}')"
                                                   min="0" step="0.01">  <!-- Se asegura de no permitir valores negativos -->

                                            <button type="button" class="btn btn-primary btn-sm mt-1"
                                                    onclick="guardarElementosCorrectos('{{ $alumno->id }}', '{{ $tipo }}', {{ $i }})">
                                                Guardar
                                            </button>
                                        @endif
                                    </th>
                                @endforeach

                                <th><i class="bi bi-clipboard-check"></i> Calificación Final</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach ($alumnos as $alumno)
                        @php
                            $calificaciones = $calificacionesPorPestania[$tipo][$alumno->id] ?? null;
                        @endphp
        <tr>
            <td>{{ $alumno->nombre_alumno }}</td>

            @foreach (range(1, ($tipo == "apoyo_p") ? 5 : 3) as $i)
                @php
                    $campoEvaluacion = 'evaluacion_' . $i;
                    $campoValorMaximo = 'valor_maximo' . $i;
                    $valorCorrecto = isset($calificacionesPorPestania[$tipo][$alumno->id]) 
                                     ? ($calificacionesPorPestania[$tipo][$alumno->id]->{'valor_maximo' . $i} ?? '') 
                                     : '';
                @endphp
                <td class="text-center">
                    <input type="number" class="form-control"
                           name="evaluaciones[{{ $alumno->id }}][eval{{ $i }}]"
                           id="input-{{ $tipo }}-{{ $alumno->id }}-{{ $i }}"
                           min="0" max="999" step="0.1"
                           disabled
                           data-id="{{ $alumno->id }}"
                           data-index="{{ $i }}"
                           value="{{ $calificaciones->$campoEvaluacion ?? '' }}">

                    @if ($tipo != "apoyo_p")
                        <div>
                            <!-- Total de elementos correctos -->
                            <input type="number" class="form-control mt-1" 
                                   id="elementos_correctos_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}" 
                                   placeholder="Total de elementos correctos" 
                                   value="{{ $valorCorrecto }}"
                                   data-correctos="{{ $valorCorrecto }}"
                                   oninput="calcularCalificacion('{{ $alumno->id }}', {{ $i }}, '{{ $tipo }}')"
                                   min="0" step="0.01"> <!-- Se asegura de no permitir valores negativos -->

                            <!-- Totales -->
                            <input type="number" class="form-control mt-1" 
                                   id="elementos_totales_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}" 
                                   placeholder="Totales" 
                                   min="0" step="0.01" 
                                   oninput="calcularCalificacion('{{ $alumno->id }}', {{ $i }}, '{{ $tipo }}')"> <!-- Se asegura de no permitir valores negativos -->

                            <!-- Mostrar Calificación -->
                            <p class="mt-1">Calificación: 
                                <span id="calificacion_{{ $alumno->id }}_{{ $i }}">-</span>
                            </p>
                        </div>
                    @endif

                    <button type="button" class="btn btn-primary btn-sm mt-1"
                            onclick="toggleEditable('{{ $tipo }}', '{{ $alumno->id }}', {{ $i }})">
                        Activar
                    </button>
                    </td>
                @endforeach

                                <td class="text-center">
                                    <small class="text-muted">Calificación total: {{ $calificaciones->Total ?? 'N/D' }}</small> <br>    
                                    <span class="porcentaje-obtenido" 
                                        data-tipo="{{ $tipo }}" 
                                        data-total="{{ $calificaciones->Total ?? 0 }}">
                                        <strong>-</strong>
                                    </span>
                                    <br>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    </table>
                </div>

                <button type="submit" class="btn btn-success mt-3 guardar-calificaciones">Guardar Calificaciones</button>
            </form>
        </div>
    @endforeach
</div>

<div class="alert alert-info mt-4 text-center" role="alert">
    <strong>Nota:</strong> Recuerda que al cerrar el acta, hará que todas las calificaciones no se puedan editar.
    
<!-- Botón para cerrar el acta -->
<form id="form-cerrar" action="{{ route('cerrar.acta') }}" method="POST">
    @csrf
    <input type="hidden" name="grado" value="{{ request('grado') }}">
    <input type="hidden" name="grupo" value="{{ request('grupo') }}">
    <button type="submit" class="btn btn-danger mb-3">Cerrar Acta</button>
</form>

<!-- Botón para abrir el modal -->
<form id="form-descargar" action="{{ route('descargar.acta') }}" method="GET">
    <input type="hidden" name="grado" value="{{ request('grado') }}">
    <input type="hidden" name="grupo" value="{{ request('grupo') }}">
    <input type="hidden" name="periodo" id="periodo-input"> <!-- Aquí guardamos el período -->

    <button type="button" class="btn btn-success" onclick="pedirPeriodo()">Descargar PDF</button>
</form>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputsPeso = document.querySelectorAll('.peso-tabla');
    const totalDisplay = document.getElementById('totalPonderacion');
    const alertaTotal = document.getElementById('alertaTotal');
    const btnGuardarPonderacion = document.getElementById('btnGuardar');
    const btnsGuardarCalificaciones = document.querySelectorAll('.guardar-calificaciones');

    // Función para actualizar el total de ponderaciones
    function actualizarTotal() {
        let total = 0;

        inputsPeso.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });

        totalDisplay.textContent = total.toFixed(2);

        if (total !== 100) {
            totalDisplay.style.color = 'red';
            alertaTotal.style.display = 'block';
            alertaTotal.textContent = 'La suma de las ponderaciones debe ser exactamente 100.';
            btnGuardarPonderacion.disabled = true;
            btnsGuardarCalificaciones.forEach(btn => btn.disabled = true);
        } else {
            totalDisplay.style.color = '';
            alertaTotal.style.display = 'none';
            btnGuardarPonderacion.disabled = false;
            btnsGuardarCalificaciones.forEach(btn => btn.disabled = false);
        }
    }

    // Escuchar cambios en los inputs
    inputsPeso.forEach(input => {
        input.addEventListener('input', actualizarTotal);
    });

    // Validar antes de enviar ponderaciones
    const formularioPonderaciones = btnGuardarPonderacion.closest('form');
    formularioPonderaciones.addEventListener('submit', function (e) {
        const total = parseFloat(totalDisplay.textContent);
        if (total !== 100) {
            e.preventDefault();
            alert('La suma de los rubros debe ser exactamente 100. Por favor, ajusta los valores.');
        }
    });

    // Activar input editable
    window.toggleEditable = function (tipo, alumnoId, evaluacionIndex) {
        const input = document.getElementById(`input-${tipo}-${alumnoId}-${evaluacionIndex}`);
        const warning = document.getElementById(`warning-${alumnoId}-${evaluacionIndex}`);
        input.disabled = !input.disabled;
        if (!input.disabled) warning.style.display = 'none';
        input.focus();
    };

    // Validar calificaciones
    btnsGuardarCalificaciones.forEach(btn => {
        btn.addEventListener('click', function (e) {
            let form = btn.closest('form');
            let inputsVacios = true;

            form.querySelectorAll('input[type="number"][name^="evaluaciones"]').forEach(input => {
                if (input.value !== "" && !input.disabled) {
                    inputsVacios = false;
                    if (input.id.includes('apoyo_p')) {
                        const val = parseFloat(input.value);
                        const warning = document.getElementById(`warning-${input.dataset.id}-${input.dataset.index}`);
                        if (val < 0 || val > 1) {
                            warning.style.display = 'inline';
                            e.preventDefault();
                        } else {
                            warning.style.display = 'none';
                        }
                    }
                }
            });

            if (inputsVacios) {
                e.preventDefault();
                alert('Error, campos vacíos o sin activar. Por favor, completa al menos un campo de evaluación antes de guardar.');
            }
        });
    });

    actualizarTotal(); // inicializa suma
});
document.addEventListener('DOMContentLoaded', () => {
    function calcularPorcentajes() {
        // Obtener los rubros desde inputs
        const rubros = {};
        document.querySelectorAll('.peso-tabla').forEach(input => {
            const tipo = input.dataset.tipo;
            const valor = parseFloat(input.value) || 0;
            rubros[tipo] = valor;
        });

        // Calcular y mostrar porcentajes
        document.querySelectorAll('.porcentaje-obtenido').forEach(span => {
            const tipo = span.dataset.tipo;
            const total = parseFloat(span.dataset.total) || 0;
            const rubroValor = rubros[tipo] || 0;
            const porcentaje = (total * rubroValor / 100).toFixed(2);
            span.innerHTML = `<strong>${porcentaje}%</strong>`;
        });
    }

    // Calcular al cargar
    calcularPorcentajes();

    // Recalcular al cambiar inputs
    document.querySelectorAll('.peso-tabla').forEach(input => {
        input.addEventListener('input', calcularPorcentajes);
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const grado = '{{ request('grado') }}';
    const grupo = '{{ request('grupo') }}';
    const clave = `pdfDescargado_${grado}_${grupo}`;

    // Intercepta la descarga y marca como descargado
    const formDescargar = document.getElementById('form-descargar');
    if (formDescargar) {
        formDescargar.addEventListener('submit', function () {
            localStorage.setItem(clave, 'true');
        });
    }

    // Advertencia al cerrar acta
    const formCerrar = document.getElementById('form-cerrar');
    if (formCerrar) {
        formCerrar.addEventListener('submit', function () {
            alert('Advertencia: al cerrar el acta, ya no se podrán editar las calificaciones.');
            console.log('[INFO] Cierre de acta enviado con advertencia.');
        });
    }
});





function guardarElementosCorrectos(alumnoId, tipoPestania, evaluacionIndex) {
    // Obtener el valor de los elementos correctos para la evaluación
    const correctos = parseFloat(document.getElementById(`elementos_correctos_${alumnoId}_${evaluacionIndex}_${tipoPestania}`).value) || 0;

    // Crear el objeto de datos para enviar al backend
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Token CSRF
        alumnos: [
            {
                id: alumnoId,
                evaluaciones: [
                    {
                        evaluacion: evaluacionIndex,
                        elementos_correctos: correctos
                    }
                ]
            }
        ],
        tipo_pestania: tipoPestania // El tipo de pestaña (por ejemplo, "proyectos")
    };

    // Realizar la solicitud al backend
    fetch('/guardar-elementos-correctos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data), // Convertir los datos a formato JSON
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert('Guardado correctamente');
        } else {
            alert('Error al guardar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un problema con la solicitud');
    });
}



function calcularCalificacion(alumnoId, evaluacionIndex, tipoPestania) {
    const correctosInput = document.getElementById(`elementos_correctos_${alumnoId}_${evaluacionIndex}_${tipoPestania}`);
    const totalesInput = document.getElementById(`elementos_totales_${alumnoId}_${evaluacionIndex}_${tipoPestania}`);

    if (!correctosInput || !totalesInput) {
        console.warn("No se encontraron los campos total de elementos correctos o totales");
        return;
    }

    const correctos = parseFloat(correctosInput.value) || 0;
    const totales = parseFloat(totalesInput.value) || 0;

    let resultado = '-';
    if (totales > 0) {
        resultado = ((totales / correctos) * 100).toFixed(2) + '%';
    }

    const resultadoSpan = document.getElementById(`calificacion_${alumnoId}_${evaluacionIndex}`);
    if (resultadoSpan) {
        resultadoSpan.textContent = resultado;
    }
}

// Mostrar el modal cuando el usuario quiera descargar
function pedirPeriodo() {
    // Limpiar el campo antes de mostrar el modal
    document.getElementById('periodo').value = '';

    var modal = new bootstrap.Modal(document.getElementById('modalPeriodo'));
    modal.show();
}

// Cuando se da click en "Guardar" dentro del modal
document.getElementById('btn-guardar-periodo').addEventListener('click', function () {
    var periodo = document.getElementById('periodo').value.trim();

    if (periodo !== "") {
        document.getElementById('periodo-input').value = periodo;

        // Cerrar el modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalPeriodo'));
        modal.hide();

        // Enviar el formulario
        document.getElementById('form-descargar').submit();
    } else {
        alert('Debes ingresar un período para descargar el PDF.');
    }
});
</script>

@endsection
