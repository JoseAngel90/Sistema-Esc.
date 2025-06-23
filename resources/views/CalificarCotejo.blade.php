<!-- Modal para mostrar el resumen de calificaciones -->
<div class="modal fade" id="modalResumen" tabindex="-1" aria-labelledby="modalResumenLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalResumenLabel">
          <i class="bi bi-file-person"></i> 📊 Resumen de Calificaciones
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="tablaResumenContainer" class="table-responsive">
          <!-- Aquí se carga la tabla de resumen dinámicamente -->
        </div>
      </div>
    </div>
  </div>
</div>



@extends('layouts.app')

@section('content')
<style>

    

    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        background: #f8f9fa;
    }

    .modal-header {
        background: linear-gradient(to right, #0062E6, #33AEFF);
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        padding: 1rem 1.5rem;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1.25rem;
    }

    .modal-body {
        padding: 2rem;
    }

    .btn-close {
        filter: brightness(0) invert(1); /* icono blanco */
    }

    .grid-container {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.25rem; /* espacio entre columnas y filas */
  }

  @media (max-width: 992px) {
    .grid-container {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  @media (max-width: 576px) {
    .grid-container {
      grid-template-columns: repeat(1, 1fr);
    }
  }

  .nav-tabs {
  border-bottom: none;
  background-color: #f8f9fa;
  padding: 0.5rem;
  border-radius: 10px;
  box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
}

/* Cada botón/pestaña */
.nav-tabs .nav-link {
  margin-right: 8px;
  background-color: #ffffff;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  color: #495057;
  padding: 0.5rem 1rem;
  transition: all 0.3s ease;
}

/* Hover para dar sensación interactiva */
.nav-tabs .nav-link:hover {
  background-color: #e9ecef;
  border-color: #adb5bd;
}

/* Activo: más notorio, oscuro y definido */
.nav-tabs .nav-link.active {
  background-color: #0d6efd;
  color: #fff;
  border-color: #0b5ed7;
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

 .oculto {
        display: none;
    }


    .resumen-tabla {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
    background-color: #fff;
    color: #333;
    border-radius: 8px;
    overflow: hidden;
}

.resumen-tabla th {
    background-color: #f4f6f9;
    color: #111;
    font-weight: 600;
    text-align: center;
    padding: 0.75rem;
    border-bottom: 1px solid #ddd;
}

.resumen-tabla td {
    text-align: center;
    padding: 0.65rem;
    border-bottom: 1px solid #eee;
}

.resumen-tabla tbody tr:hover {
    background-color: #f9f9f9;
}

.resumen-tabla tbody tr:nth-child(even) {
    background-color: #fafafa;
}

.resumen-tabla td strong {
    color: #111;
    font-weight: bold;
}

.resumen-tabla .badge {
    background-color: #eaeaea;
    color: #333;
    font-weight: 500;
    font-size: 0.8rem;
    border-radius: 6px;
    padding: 0.3rem 0.5rem;
    display: inline-block;
    margin-top: 0.25rem;
}

#buscadorResumen {
    width: 100%;
    max-width: 340px;
    margin: 0 auto 1rem auto;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #fff;
    font-size: 0.95rem;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
    background-image: url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/search.svg');
    background-repeat: no-repeat;
    background-position: 0.6rem center;
    background-size: 1rem;
    padding-left: 2.2rem;
    color: #333;
}

</style>



<!-- Botón para regresar a Evaluación -->
<a href="{{ route('panel', ['grado' => $gradoFiltro, 'grupo' => $grupoFiltro]) }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left-circle"></i> Regresar al Panel
</a>
<br>
<div class="text-center mt-3">
    <h2>Criterios de evaluación.</h2>
    <h3>Valor de cada criterio en porcentaje.</h3>
    <br>
</div>



<!-- Formulario para guardar ponderaciones -->
<form id="formRubro" action="{{ route('guardar.rubro') }}" method="POST">
    @csrf
    <input type="hidden" name="alumno_id" value="{{ auth()->user()->alumno_id }}">
    <input type="hidden" name="grado" value="{{ $gradoFiltro }}">
    <input type="hidden" name="grupo" value="{{ $grupoFiltro }}">

    @php
    $tipos = ['apoyo_p', 'proyectos', 'trabajos_clase', 'tareas', 'examen'];
  
    @endphp

<div class="container-fluid">
  <div class="grid-container mb-4">
    @foreach ($tipos as $index => $tipo)
      <div class="border rounded p-4 shadow-sm h-100 bg-light">

        <label class="fw-bold text-primary mb-3 d-block" for="peso-Rubro{{ $index + 1 }}">
          Criterio {{ $index + 1 }}
        </label>

        <input type="number" class="form-control peso-tabla mb-4"
               id="peso-Rubro{{ $index + 1 }}"
               name="Rubro{{ $index + 1 }}"
               data-tipo="{{ $tipo }}"
               value="{{ old('Rubro' . ($index + 1), $registro->{'rubro' . ($index + 1)} ?? '') }}"
               min="0" step="0.01"
               placeholder="Valor">

        @php
            $etiqueta = $etiquetas->firstWhere('etiqueta_nombre', 'rubro' . ($index + 1));
        @endphp

        <!-- Nombre del criterio (etiqueta_rubro) -->
        <label for="etiqueta-Rubro{{ $index + 1 }}" class="form-label text-secondary mb-2">
          Nombre del criterio
        </label>
        <input type="text" class="form-control mb-4"
               id="etiqueta-Rubro{{ $index + 1 }}"
               name="etiqueta-Rubro{{ $index + 1 }}"
               value="{{ old('etiqueta-Rubro' . ($index + 1), $etiqueta->etiqueta_rubro ?? '') }}"
               placeholder="Nombre del criterio">

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
            <button type="submit" id="btnGuardar" class="btn btn-primary mt-3">Guardar Ponderación/Nombres</button>
        </div>
    </div>
</form>
<br>
    <div class="alert alert-warning">
        <strong>Advertencia:</strong> Asegúrese de ingresar los datos correctos para evitar errores.
    </div>


    <button type="button"
        class="mostrar-resumen"
        style="
            background: linear-gradient(to right, #00b4db, #0083b0);
            border: none;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        "
        onmouseover="this.style.transform='scale(1.05)'"
        onmouseout="this.style.transform='scale(1)'"
    >
        📋 Mostrar Resumen
    </button>


<!-- Pestañas -->
<ul class="nav nav-tabs" id="calificacionesTabs" role="tablist">
    @foreach (["apoyo_p", "proyectos", "trabajos_clase", "tareas", "examen"] as $index => $tipo)
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tipo }}-tab" data-bs-toggle="tab" 
               href="#{{ $tipo }}" role="tab" aria-controls="{{ $tipo }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                Criterio {{ $index + 1 }}
                @php
                    $etiqueta = $etiquetas->firstWhere('etiqueta_nombre', 'rubro' . ($index + 1));
                @endphp
        
        <input type="text" class="form-control mt-1"
               disabled="disabled"
               id="etiqueta-Rubro{{ $index + 1 }}"
               name="etiqueta-Rubro{{ $index + 1 }}"
               value="{{ old('etiqueta-Rubro' . ($index + 1), $etiqueta->etiqueta_rubro ?? '') }}"
               placeholder="Nombre del criterio">


       
            </a>
        </li>
    @endforeach
</ul>



<!-- Contenido de pestañas -->
<div class="tab-content" id="calificacionesTabsContent">
    @foreach (["apoyo_p", "proyectos", "trabajos_clase", "tareas", "examen"] as $pestaniaIndex => $tipo)
    <!-- @if ($tipo == "apoyo_p")
    <br>
    <h2>Aspecto de evaluacion de criterio.</h2>
    <div class="alert alert-info" role="alert">
        <strong>Nota:</strong> Al guardar calificaciones debes activar las que tengan calificacion.
    </div>
    <div class="alert alert-warning" role="alert">
        <strong>Importante:</strong> Si vuelves a guardar otra calificación y ves un <strong>(100)</strong>, cámbialo manualmente a <strong>(1). <br> Unicamente para Criterio 1</strong>.
    </div>
@endif -->

        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tipo }}" role="tabpanel" 
             aria-labelledby="{{ $tipo }}-tab">

              @if ($tipo == "apoyo_p")
                <br>
                <h2>Aspecto de evaluacion de cotejo.</h2>
                <div class="alert alert-info" role="alert">
                    <strong>Nota:</strong> Al guardar calificaciones debes activar las que tengan calificacion.
                </div>
                <div class="alert alert-warning" role="alert">
                    <strong>Importante:</strong> Si vuelves a guardar otra calificación y ves un <strong>(100)</strong>, cámbialo manualmente a <strong>(1). <br> Unicamente para Evaluacion de cotejo</strong>.
                </div>
                @endif
                    @php
                    $rubrosPorTipo = [
                        'apoyo_p' => 'rubro1',
                        'proyectos' => 'rubro2',
                        'trabajos_clase' => 'rubro3',
                        'tareas' => 'rubro4',
                        'examen' => 'rubro5',
                    ];
                    $rubroActual = $rubrosPorTipo[$tipo] ?? null;
                    $etiquetaActual = $etiquetas->firstWhere('etiqueta_nombre', $rubroActual);
                @endphp
                <br>
                <h3>
                    Aspecto de evaluacion: {{ $etiquetaActual && $etiquetaActual->etiqueta_rubro ? $etiquetaActual->etiqueta_rubro : 'Evaluación' }}
                </h3>

            <form action="{{ route('calificaciones.guardar') }}" method="POST">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" name="tipo_pestania" value="{{ $tipo }}">
                <input type="hidden" name="grado" value="{{ $gradoFiltro }}">
                <input type="hidden" name="grupo" value="{{ $grupoFiltro }}">    

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover shadow-sm">
                        <thead class="table-primary text-center">
                            <tr>
                                <th><i class="bi bi-person-fill"></i> Alumno</th>

                                <!-- Generar las columnas de las evaluaciones por pestaña -->
                                @foreach (range(1, ($tipo == "apoyo_p") ? 5 : 3) as $i)
                                
                                    <th>
                                        <i class="bi bi-pen"></i>
                                        <input
                                            type="text"
                                            class="editable-title"
                                            name="aspectos[{{ $i }}][{{ $tipo }}]"
                                            id="etiqueta_aspecto_{{ $i }}_{{ $tipo }}"
                                            value="{{ old('aspectos.' . $i . '.' . $tipo, $aspectos[$i][$tipo] ?? 'Aspecto ' . $i) }}"
                                            placeholder="Aspecto {{ $i }}"
                                            style="border: 1.5px solid #6c757d; background: rgba(255, 255, 255, 0.8); font-weight: 600; color: #333; width: 120px; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);"
                                            maxlength="255"
                                        />

                                        @if ($tipo != "apoyo_p")
                                            <!-- Input de Total de elementos correctos debajo de la evaluación -->
                                            <input type="number"
                                                class="form-control mt-1"
                                                id="elementos_correctos_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}"
                                                name="elementos_correctos[{{ $alumno->id }}][{{ $i }}][{{ $tipo }}]"
                                                placeholder="Valor Máximo"
                                                value="{{ $calificacionesPorPestania[$tipo][$alumno->id]->{'valor_maximo' . $i} ?? '' }}"
                                                data-grado="{{ $alumno->grado }}"
                                                data-grupo="{{ $alumno->grupo }}"
                                                oninput="calcularCalificacion('{{ $alumno->id }}', {{ $i }}, '{{ $tipo }}'); sincronizarHiddenCorrectos('{{ $alumno->id }}', {{ $i }}, '{{ $tipo }}')"
                                                min="0" step="0.01"
                                            >

                                            
                                                   
                                            <!-- Input oculto para enviar el valor máximo al guardar calificaciones -->
                                            <input type="hidden"
                                                name="elementos_correctos[{{ $alumno->id }}][{{ $i }}][{{ $tipo }}]"
                                                id="hidden_elementos_correctos_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}"
                                                value="{{ $calificacionesPorPestania[$tipo][$alumno->id]->{'valor_maximo' . $i} ?? '' }}">

                                            <button type="button" class="btn btn-primary btn-sm mt-1"
                                                    onclick="guardarElementosCorrectos('{{ $alumno->id }}', '{{ $tipo }}', {{ $i }}, {{ $pestaniaIndex }})">
                                                Guardar Valor Máximo
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
                    <div class="d-flex align-items-center">
                        @if ($tipo !== 'apoyo_p')
                            <span class="me-3">Calificación: </span>
                        @endif

                        @php
                            $valor = $calificaciones->$campoEvaluacion ?? null;
                            $color = $tipo != 'apoyo_p' ? 'background-color:#ebebef' : '';
                        @endphp

                        <input type="number" class="form-control"
                            name="evaluaciones[{{ $alumno->id }}][eval{{ $i }}]"
                            id="input-{{ $tipo }}-{{ $alumno->id }}-{{ $i }}"
                            min="0" max="999" step="0.1"
                            style="{{ $color }}"
                            tabindex="-1"
                            @if ($tipo == 'apoyo_p') disabled @endif                            
                            value="{{ $valor }}"
                            data-id="{{ $alumno->id }}"
                            data-index="{{ $i }}"
                            oninput="calcularCalificacion('{{ $alumno->id }}', {{ $i }}, '{{ $tipo }}')"
                        >

                    </div>
                           @if ($tipo == "apoyo_p")
                                <span id="visual_uno_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}" style="font-weight:bold; color:#28a745; margin-left:5px;">
                                    @if(($calificacionesPorPestania[$tipo][$alumno->id]->{'valor_maximo' . $i} ?? '') == 1) 1 @endif
                                </span>
                                <div style="color: #b22222; font-size: 0.9em; margin-top: 2px;">
                                    <strong>Advertencia:</strong> Solo ingresa un <b>1</b> o <b>0</b>
                                </div>
                                <!-- Botón solo para apoyo_p -->
                                <button type="button"
                                    class="btn btn-success btn-sm mt-1"
                                    id="btn-activar-{{ $tipo }}-{{ $alumno->id }}-{{ $i }}"
                                    onclick="toggleEditable('{{ $tipo }}', '{{ $alumno->id }}', {{ $i }})">
                                    Ingresar dato
                                </button>
                            @endif

                    @if ($tipo != "apoyo_p")
                        <div>

                        
                            <!-- Total de elementos correctos -->

                                <span id="valor_maximo_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}" style="display: none;">
                                    {{ $calificacionesPorPestania[$tipo][$alumno->id]->{'valor_maximo' . $i} ?? '0' }}
                                </span>


                            <!-- Totales -->
                            <div class="d-flex align-items-center">
                            @if ($tipo != "apoyo_p")
                                <span class="me-3">Entregables: </span>
                            @endif
                                    <input type="number" class="form-control mt-1" 
                                        id="elementos_totales_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}" 
                                        name="elementos_totales[{{ $alumno->id }}][{{ $i }}][{{ $tipo }}]"
                                        value="{{ $calificacionesPorPestania[$tipo][$alumno->id]->{'entregables_' . $i} ?? '' }}"
                                        min="0" step="0.01" 
                                        oninput="calcularCalificacion('{{ $alumno->id }}', {{ $i }}, '{{ $tipo }}')"> <!-- Se asegura de no permitir valores negativos -->
                            </div>
                                <!-- Mostrar Calificación -->
                                <!-- <p class="mt-1">Calificación: 
                                    <span  id="calificacion_{{ $alumno->id }}_{{ $i }}_{{ $tipo }}">-</span>
                                </p> -->
                            </div>
                        @endif

                        <!-- Botón para activar/desactivar el input -->
                            <!-- <button type="button"
                                class="btn btn-success btn-sm mt-1"
                                id="btn-activar-{{ $tipo }}-{{ $alumno->id }}-{{ $i }}"
                                onclick="toggleEditable('{{ $tipo }}', '{{ $alumno->id }}', {{ $i }})">
                                Ingresar dato
                            </button> -->
                        </td>
                    @endforeach

                                <td class="text-center">
                                    <small class="text-muted">Calificación total: {{ $calificaciones->Total ?? 'N/D' }}</small> <br>    
                                    <br>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    </table>
                </div>

                <button type="submit" class="btn btn-success mt-3 guardar-calificaciones">Guardar Calificaciones / Nombre de aspectos</button>
            </form>
        </div>
    @endforeach
</div>

<div class="alert alert-info mt-4 text-center" role="alert">
    <strong>Nota:</strong> Recuerda que al cerrar el acta, hará que todas las calificaciones no se puedan editar.
    

<!-- Botón para abrir el modal -->
<form id="form-descargar" action="{{ route('descargar.acta') }}" method="GET">
    <input type="hidden" name="grado" value="{{ request('grado') }}">
    <input type="hidden" name="grupo" value="{{ request('grupo') }}">
<button type="submit" class="btn btn-success">Descargar PDF</button></form>

<!-- Botón para cerrar el acta -->
<form id="form-cerrar" action="{{ route('cerrar.acta') }}" method="POST">
    @csrf
    <input type="hidden" name="grado" value="{{ request('grado') }}">
    <input type="hidden" name="grupo" value="{{ request('grupo') }}">
    <button type="submit" class="btn btn-danger mb-3">Cerrar Acta</button>
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
        const btn = document.getElementById(`btn-activar-${tipo}-${alumnoId}-${evaluacionIndex}`);
        const warning = document.getElementById(`warning-${alumnoId}-${evaluacionIndex}`);
        // Alterna el estado del input
        input.disabled = !input.disabled;

        if (!input.disabled) {
            // El input ahora está habilitado → mostrar opción para DESACTIVAR
            if (warning) warning.style.display = 'none';
            input.focus();

            btn.classList.remove('btn-success');
            btn.classList.add('btn-danger');
            btn.textContent = 'Desactivar';
        } else {
            // El input ahora está deshabilitado → mostrar opción para ACTIVAR
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-success');
            btn.textContent = 'Ingresar  dato';
        }



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




function guardarElementosCorrectos(alumnoId, tipoPestania, evaluacionIndex, pestaniaIndex) {
    const inputCorrectos = document.getElementById(`elementos_correctos_${alumnoId}_${evaluacionIndex}_${tipoPestania}`);
    if (!inputCorrectos) {
        alert('No se encontró el input de elementos correctos');
        return;
    }
    const correctos = parseFloat(inputCorrectos.value) || 0;

    // Suponiendo que tienes grado y grupo guardados en atributos data-grado y data-grupo del input, por ejemplo:
    const grado = inputCorrectos.getAttribute('data-grado');
    const grupo = inputCorrectos.getAttribute('data-grupo');

    if (!grado || !grupo) {
        alert('No se encontró grado o grupo para el alumno');
        return;
    }

    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        grado: grado,
        grupo: grupo,
        tipo_pestania: tipoPestania,
        pestania_index: pestaniaIndex,
        evaluacion: evaluacionIndex,
        elementos_correctos: correctos
    };

    fetch('/guardar-elementos-correctos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': data._token
        },
        body: JSON.stringify(data),
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert('Guardado correctamente');
            location.reload();
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
    const valorMaximoSpan = document.getElementById(`valor_maximo_${alumnoId}_${evaluacionIndex}_${tipoPestania}`);
    const totalesInput = document.getElementById(`elementos_totales_${alumnoId}_${evaluacionIndex}_${tipoPestania}`);

    if (!valorMaximoSpan || !totalesInput) {
        console.warn("No se encontraron los campos valor máximo o totales");
        return;
    }

    const correctos = parseFloat(valorMaximoSpan.textContent) || 0;
    const totales = parseFloat(totalesInput.value) || 0;

    let resultado = '-';
    if (totales > 0 && correctos > 0) {
        const calculo = (totales / correctos) * 100;
        resultado = Math.min(calculo, 100).toFixed(1);
    }

    // Actualizar el <span> de calificación
    const resultadoSpan = document.getElementById(`calificacion_${alumnoId}_${evaluacionIndex}_${tipoPestania}`);
    if (resultadoSpan) {
        resultadoSpan.textContent = resultado;
    }

    // Actualizar el <input> deshabilitado con la calificación
    const inputCalificacion = document.getElementById(`input-${tipoPestania}-${alumnoId}-${evaluacionIndex}`);
    if (inputCalificacion) {
        inputCalificacion.value = (resultado !== '-' ? resultado : '');
    }
}



function sincronizarHiddenCorrectos(alumnoId, i, tipo) {
    const visible = document.getElementById(`elementos_correctos_${alumnoId}_${i}_${tipo}`);
    const hidden = document.getElementById(`hidden_elementos_correctos_${alumnoId}_${i}_${tipo}`);
    if (visible && hidden) {
        hidden.value = visible.value;
    }
}

// Sincroniza todos los inputs al cargar la página (por si ya hay valores)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[id^="elementos_correctos_"]').forEach(input => {
        const parts = input.id.split('_');
        if (parts.length >= 4) {
            sincronizarHiddenCorrectos(parts[2], parts[3], parts[4]);
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.mostrar-resumen').forEach(btn => {
        btn.addEventListener('click', function () {
            let alumnos = @json($alumnos);
            let tipos = @json($tipos);
            let calificacionesPorPestania = @json($calificacionesPorPestania);
            let registro = @json($registro);
            let etiquetas = @json($etiquetas);

            function construirTabla(filtro = '') {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-bordered resumen-tabla">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    ${etiquetas.map((etiqueta, i) => `
                                        <th>
                                            ${etiqueta.etiqueta_rubro}<br>
                                            <span class="porcentaje-obtenido badge bg-warning text-dark fw-bold">
                                                ${(registro && registro['rubro' + (i + 1)] !== undefined) ? registro['rubro' + (i + 1)] : 0}%
                                            </span>
                                        </th>
                                    `).join('')}
                                    <th>Calificación final</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${alumnos
                                    .filter(alumno => alumno.nombre_alumno.toLowerCase().includes(filtro.toLowerCase()))
                                    .map(alumno => {
                                        let total = 0;
                                        let row = `<tr><td><strong>${alumno.nombre_alumno}</strong></td>`;
                                        tipos.forEach((tipo, i) => {
                                            let calif = calificacionesPorPestania[tipo] &&
                                                        calificacionesPorPestania[tipo][alumno.id]
                                                ? (calificacionesPorPestania[tipo][alumno.id].Total ?? '-')
                                                : '-';
                                            let ponderacion = registro ? (registro['rubro' + (i + 1)] ?? 0) : 0;
                                            let parcial = (calif !== '-' && ponderacion) ? (Number(calif) * ponderacion / 100) : 0;
                                            total += (parcial || 0);
                                            row += `<td>${calif !== '-' ? Number(calif).toFixed(2) : '-'}</td>`;
                                        });
                                        let mostrar = total ? total.toFixed(2) : '-';
                                        row += `<td><strong>${mostrar}</strong></td></tr>`;
                                        return row;
                                    }).join('')}
                            </tbody>
                        </table>
                    </div>`;
                return html;
            }

            // Mostrar buscador y tabla
            document.getElementById('tablaResumenContainer').innerHTML = `
                <div class="mb-3">
                        <input type="text" id="buscadorResumen" class="form-control mb-3" placeholder="🔍 Buscar alumno.">
                        <div id="contenidoTablaResumen">
                        ${construirTabla()}
                    </div>
                </div>
            `;

            setTimeout(() => {
                const buscador = document.getElementById('buscadorResumen');
                if (buscador) {
                    buscador.addEventListener('input', function () {
                        const filtro = this.value;
                        document.getElementById('contenidoTablaResumen').innerHTML = construirTabla(filtro);
                    });
                }
            }, 100);

            new bootstrap.Modal(document.getElementById('modalResumen')).show();
        });
    });
});





// Guardar la pestaña activa en localStorage
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('#calificacionesTabs .nav-link');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            localStorage.setItem('pestaniaActivaCalificarCotejo', e.target.id);
        });
    });

    // Al cargar la página, activar la pestaña guardada
    const pestaniaGuardada = localStorage.getItem('pestaniaActivaCalificarCotejo');
    if (pestaniaGuardada) {
        const tab = document.getElementById(pestaniaGuardada);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }
});


</script>

@endsection
