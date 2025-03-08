@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">Bienvenido a tu panel principal</h1>
    <p class="text-center">Administrador: {{ Auth::user()->name }}</p>
</div>
<style>
    /* Configuración del contenedor con Grid */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Disposición automática con columnas mínimas de 200px */
        grid-gap: 20px; /* Espaciado entre elementos */
        padding: 20px; /* Padding general */
    }

    /* Estilos generales para los elementos de la cuadrícula */
    .grid-item {
        background-color: #f0f0f0; /* Fondo gris claro */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease, background-color 0.3s ease; /* Efecto de animación */
    }

    .grid-item:hover {
        transform: scale(1.05); /* Efecto de hover */
        background-color: #e0e0e0; /* Cambio de color al hacer hover */
    }

    /* Estilos para el encabezado */
    h1 {
        color: #333;
        font-size: 2rem;
    }

    .grupo-titulo {
        font-size: 1.5rem;
        color: #333;
    }

    /* Animaciones para mostrar/ocultar secciones */
    .fade {
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    .fade.show {
        opacity: 1;
    }

    /* Animaciones para botones */
    .btn {
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn:hover {
        transform: scale(1.05);
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Mostrar mensajes de éxito -->
            @if(session('success'))
                <div class="alert alert-success fade show">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar errores si existen -->
            @if ($errors->any())
                <div class="alert alert-danger fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Crear grupo -->
            <div class="d-flex justify-content-center mb-4">
                <button class="btn btn-primary btn-lg" id="mostrarFormulario">
                    <i class="fas fa-plus-circle"></i> Crear Grupo
                </button>
            </div>

            <!-- Formulario oculto -->
            <div id="formularioGrupo" class="fade" style="display: none; margin-top: 20px;">
                <div class="card shadow-lg border-primary mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Registrar Nuevo Grupo</strong>
                    </div>
                    <div class="card-body text-primary">
                        <form action="{{ route('grupo.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="grado">Grado</label>
                                <input type="number" name="grado" class="form-control" placeholder="Ejemplo: 1" required min="1" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                            <div class="form-group">
                                <label for="grupo">Grupo</label>
                                <input type="text" name="grupo" class="form-control" placeholder="Ejemplo: A" required oninput="this.value = this.value.replace(/[^A-Za-z]/g, '').toUpperCase()">
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success mt-2" onclick="alert('Grupo guardado exitosamente')">Guardar Grupo</button>
                                <button type="button" class="btn btn-danger mt-2" id="cancelar">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Botón para mostrar los datos del grupo -->
            <div class="d-flex justify-content-center mb-4">
                <button class="btn btn-info btn-lg" id="mostrarDatosGrupo">
                    <i class="fas fa-info-circle"></i> Datos del Grupo
                </button>
            </div>

            <!-- Contenedor con los datos del grupo, inicialmente oculto -->
            <div id="datosGrupo" class="fade" style="display: none;">
                @if($datosGenerales)
                    <div class="card shadow-lg border-primary mb-3">
                        <div class="card-header bg-primary text-white">Datos del Grupo</div>
                        <div class="card-body text-primary">
                            <p><strong>Escuela:</strong> {{ $datosGenerales->nombre_escuela }}</p>
                            <p><strong>Ciclo Escolar:</strong> {{ $datosGenerales->ciclo_escolar }}</p>
                            <p><strong>Turno:</strong> {{ $datosGenerales->turno }}</p>
                            <p><strong>Asignatura:</strong> {{ $datosGenerales->asignatura }}</p>
                            <p><strong>Profesor:</strong> {{ $datosGenerales->nombre_profesor }}</p>
                            @if($datosGenerales->user_id == Auth::id())
                                <button class="btn btn-warning" id="editButton">
                                    <i class="fas fa-edit"></i> Editar Datos
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($datosGenerales->user_id == Auth::id())
                        <div id="editForm" class="fade" style="display: none;">
                            <div class="card shadow-lg border-primary mb-3">
                                <div class="card-header bg-warning text-white">Editar Datos Generales</div>
                                <div class="card-body text-primary">
                                    <form action="{{ route('datosGenerales.store') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="nombre_escuela">Nombre de la Escuela</label>
                                            <input type="text" name="nombre_escuela" class="form-control" value="{{ $datosGenerales->nombre_escuela }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="ciclo_escolar">Ciclo Escolar</label>
                                            <input type="text" name="ciclo_escolar" class="form-control" value="{{ $datosGenerales->ciclo_escolar }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="turno">Turno</label>
                                            <input type="text" name="turno" class="form-control" value="{{ $datosGenerales->turno }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="asignatura">Asignatura</label>
                                            <input type="text" name="asignatura" class="form-control" value="{{ $datosGenerales->asignatura }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="nombre_profesor">Nombre del Profesor</label>
                                            <input type="text" name="nombre_profesor" class="form-control" value="{{ $datosGenerales->nombre_profesor }}" required>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="submit" class="btn btn-success mt-2">Actualizar Datos Generales</button>
                                            <button type="button" class="btn btn-danger mt-2" id="cancelButton">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="card shadow-lg border-primary mb-3">
                        <div class="card-header bg-primary text-white">Registrar Datos Generales</div>
                        <div class="card-body text-primary">
                            <form action="{{ route('datosGenerales.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="nombre_escuela">Nombre de la Escuela</label>
                                    <input type="text" name="nombre_escuela" class="form-control" placeholder="Ejemplo: Escuela Primaria" required>
                                </div>
                                <div class="form-group">
                                    <label for="ciclo_escolar">Ciclo Escolar</label>
                                    <input type="text" name="ciclo_escolar" class="form-control" placeholder="Ejemplo: 2024-2025" required>
                                </div>
                                <div class="form-group">
                                    <label for="turno">Turno</label>
                                    <input type="text" name="turno" class="form-control" placeholder="Ejemplo: Matutino" required>
                                </div>
                                <div class="form-group">
                                    <label for="asignatura">Asignatura</label>
                                    <input type="text" name="asignatura" class="form-control" placeholder="Ejemplo: Matemáticas" required>
                                </div>
                                <div class="form-group">
                                    <label for="nombre_profesor">Nombre del Profesor</label>
                                    <input type="text" name="nombre_profesor" class="form-control" placeholder="Ejemplo: Juan Pérez" required>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-success mt-2">Guardar Datos Generales</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <div class="container mt-4">
                <h3>Grupos del Usuario</h3>
                <div class="row" id="gruposContainer">
                    @foreach ($grupos as $grupo)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4" id="grupo_{{ $grupo->id }}">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <!-- Enlace para ir al panel -->
                                        <a href="{{ route('panel', ['grado' => $grupo->grado, 'grupo' => $grupo->grupo]) }}" 
                                            class="btn btn-secondary w-100 text-center grupo-btn" id="btn_{{ $grupo->id }}">
                                            {{ $grupo->grado }} {{ $grupo->grupo }}
                                        </a>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <!-- Botón para cambiar el estado -->
                                        <button class="btn {{ $grupo->activo ? 'btn-success' : 'btn-secondary' }} btn-sm w-100" 
                                                onclick="toggleGroup({{ $grupo->id }})" id="toggleBtn_{{ $grupo->id }}">
                                            {{ $grupo->activo ? 'Activado' : 'Desactivado' }}
                                        </button>
                                    </div>

                                    <div class="mt-3">
                                        <!-- Formulario para dar de baja -->
                                        <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas dar de baja este grupo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">Dar de Baja</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Si no hay grupos disponibles -->
                    @if($grupos->isEmpty())
                        <p>No hay grupos disponibles.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/Ocultar datos del grupo
        var mostrarDatosGrupoBtn = document.getElementById('mostrarDatosGrupo');
        if (mostrarDatosGrupoBtn) {
            mostrarDatosGrupoBtn.addEventListener('click', function() {
                var datosGrupo = document.getElementById('datosGrupo');
                datosGrupo.classList.toggle('show');
                datosGrupo.style.display = datosGrupo.style.display === 'none' ? 'block' : 'none';
            });
        }

        // Mostrar el formulario de edición de datos generales
        var editButton = document.getElementById('editButton');
        if (editButton) {
            editButton.addEventListener('click', function() {
                var editForm = document.getElementById('editForm');
                editForm.classList.toggle('show');
                editForm.style.display = 'block';
                this.style.display = 'none';
            });
        }

        // Cancelar edición de datos generales
        var cancelButton = document.getElementById('cancelButton');
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                var editForm = document.getElementById('editForm');
                editForm.classList.toggle('show');
                editForm.style.display = 'none';
                document.getElementById('editButton').style.display = 'inline-block';
            });
        }

        // Mostrar/Ocultar formulario para crear grupo
        var mostrarFormularioBtn = document.getElementById('mostrarFormulario');
        if (mostrarFormularioBtn) {
            mostrarFormularioBtn.addEventListener('click', function() {
                var formulario = document.getElementById('formularioGrupo');
                formulario.classList.toggle('show');
                formulario.style.display = formulario.style.display === 'none' ? 'block' : 'none';
                this.style.display = formulario.style.display === 'none' ? 'block' : 'none';
            });
        }

        // Cancelar creación de grupo
        var cancelarBtn = document.getElementById('cancelar');
        if (cancelarBtn) {
            cancelarBtn.addEventListener('click', function() {
                var formulario = document.getElementById('formularioGrupo');
                formulario.classList.toggle('show');
                formulario.style.display = 'none';
                document.getElementById('mostrarFormulario').style.display = 'block';
            });
        }
    });

    // Función para cambiar el estado de activación/desactivación de los grupos
    function toggleGroup(grupoId) {
        const grupoBtn = document.getElementById(`btn_${grupoId}`);
        const toggleBtn = document.getElementById(`toggleBtn_${grupoId}`);

        // Cambiar el estado entre "activado" y "desactivado"
        if (toggleBtn.classList.contains('btn-success')) {
            // Si el grupo está activado, desactivarlo
            toggleBtn.classList.remove('btn-success');
            toggleBtn.classList.add('btn-secondary');
            toggleBtn.textContent = 'Desactivado';
            grupoBtn.classList.add('disabled');
            grupoBtn.removeAttribute('href'); // Desactivar el enlace

                
        } else {
            // Si el grupo está desactivado, activarlo
            toggleBtn.classList.remove('btn-secondary');
            toggleBtn.classList.add('btn-success');
            toggleBtn.textContent = 'Activado';
            grupoBtn.classList.remove('disabled');
            const grado = grupoBtn.dataset.grado;
            const grupo = grupoBtn.dataset.grupo;
            grupoBtn.href = `{{ url('panel') }}/${grado}/${grupo}`; // Cambia el URL de acuerdo a los datos
        }
    }
</script>
@endsection