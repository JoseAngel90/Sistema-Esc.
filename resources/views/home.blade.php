@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">Bienvenido a tu panel principal</h1>
    <p class="text-center">Administrador: {{ Auth::user()->name }}</p>
</div>
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        grid-gap: 20px;
        padding: 20px;
    }

    .grid-item {
        background-color: #f0f0f0;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .grid-item:hover {
        transform: scale(1.05);
        background-color: #e0e0e0;
    }

    h1, .grupo-titulo {
        color: #333;
    }

    h1 {
        font-size: 2rem;
    }

    .grupo-titulo {
        font-size: 1.5rem;
    }

    .fade {
        opacity: 0;
        transition: opacity 0.5s ease-in-out, max-height 0.5s ease-in-out;
        max-height: 0;
        overflow: hidden;
    }

    .fade.show {
        opacity: 1;
        max-height: 1000px;
    }

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
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('nuevoGrupo'))
            <div class="alert alert-info">
                Nuevo grupo creado: {{ session('nuevoGrupo') }}
            </div>
            @endif

            @if(session('grupo_eliminado'))
            <div class="alert alert-warning">
                El grupo con ID {{ session('grupo_eliminado') }} ha sido eliminado.
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
                                <input type="text" name="grado" class="form-control" placeholder="Ejemplo: 1" required 
                                    maxlength="1" 
                                    oninput="this.value = this.value.replace(/[^1-9]/g, '')">
                            </div>
                            <div class="form-group">
                                <label for="grupo">Grupo</label>
                                <input type="text" name="grupo" class="form-control" placeholder="Ejemplo: A" required 
                                    maxlength="1"
                                    oninput="this.value = this.value.replace(/[^A-Za-z]/g, '').toUpperCase()">
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success mt-2">Guardar Grupo</button>
                                <button type="button" class="btn btn-danger mt-2" id="cancelar">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                                            class="btn btn-primary w-100 text-center grupo-btn" id="btn_{{ $grupo->id }}">
                                            {{ $grupo->grado }} {{ $grupo->grupo }}
                                        </a>
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
        var datosGrupo = document.getElementById('datosGrupo');

        if (mostrarDatosGrupoBtn && datosGrupo) {
            mostrarDatosGrupoBtn.addEventListener('click', function() {
                datosGrupo.classList.toggle('show');
                datosGrupo.style.display = datosGrupo.style.display === 'none' || datosGrupo.style.display === '' ? 'block' : 'none';
            });
        }

        // Mostrar el formulario de edición de datos generales
        var editButton = document.getElementById('editButton');
        var editForm = document.getElementById('editForm');

        if (editButton && editForm) {
            editButton.addEventListener('click', function() {
                editForm.classList.add('show');
                editForm.style.display = 'block';
                this.style.display = 'none';
            });
        }

        // Cancelar edición de datos generales
        var cancelButton = document.getElementById('cancelButton');
        if (cancelButton && editForm && editButton) {
            cancelButton.addEventListener('click', function() {
                editForm.classList.remove('show');
                editForm.style.display = 'none';
                editButton.style.display = 'inline-block';
            });
        }

        // Mostrar/Ocultar formulario para crear grupo
        var mostrarFormularioBtn = document.getElementById('mostrarFormulario');
        var formularioGrupo = document.getElementById('formularioGrupo');

        if (mostrarFormularioBtn && formularioGrupo) {
            mostrarFormularioBtn.addEventListener('click', function() {
                var visible = formularioGrupo.style.display === 'block';
                formularioGrupo.classList.toggle('show');
                formularioGrupo.style.display = visible ? 'none' : 'block';
            });
        }

        // Cancelar creación de grupo
        var cancelarBtn = document.getElementById('cancelar');
        if (cancelarBtn && formularioGrupo) {
            cancelarBtn.addEventListener('click', function() {
                formularioGrupo.classList.remove('show');
                formularioGrupo.style.display = 'none';
            });
        }
    });

    
</script>
@endsection