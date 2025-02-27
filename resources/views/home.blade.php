@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container mt-5">
    <h1>Bienvenido a tu Panel de Control, {{ Auth::user()->name }}!</h1>
    <p>Esta es la página principal de tu aplicación, accesible solo después de iniciar sesión.</p>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Mostrar mensajes de éxito -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mostrar errores si existen -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Mostrar los datos generales si existen -->
            @if($datosGenerales)
                <div class="card border-primary mb-3">
                    <div class="card-header">Datos Generales Registrados</div>
                    <div class="card-body text-primary">
                        <p><strong>Nombre de la Escuela:</strong> {{ $datosGenerales->nombre_escuela }}</p>
                        <p><strong>Ciclo Escolar:</strong> {{ $datosGenerales->ciclo_escolar }}</p>
                        <p><strong>Turno:</strong> {{ $datosGenerales->turno }}</p>
                        <p><strong>Asignatura:</strong> {{ $datosGenerales->asignatura }}</p>
                        <p><strong>Grado y Grupo:</strong> {{ $datosGenerales->grado_grupo }}</p>
                        <p><strong>Profesor:</strong> {{ $datosGenerales->nombre_profesor }}</p>
                        <p><strong>Periodo:</strong> {{ $datosGenerales->periodo }}</p>
                        <!-- Botón para editar los datos -->
                        <button class="btn btn-warning" id="editButton">Editar Datos</button>
                    </div>
                </div>

                <!-- Formulario de edición, inicialmente visible -->
                @if($datosGenerales && $datosGenerales->user_id == Auth::id())
                    <div id="editForm" style="display: none;">
                        <div class="card border-primary mb-3">
                            <div class="card-header">Editar Datos Generales</div>
                            <div class="card-body text-primary">
                                <form action="{{ route('datosGenerales.store') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                        <!-- Indicamos que es una solicitud PUT para actualizar -->
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
                                        <label for="grado_grupo">Grado y Grupo</label>
                                        <input type="text" name="grado_grupo" class="form-control" value="{{ $datosGenerales->grado_grupo }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="nombre_profesor">Nombre del Profesor</label>
                                        <input type="text" name="nombre_profesor" class="form-control" value="{{ $datosGenerales->nombre_profesor }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="periodo">Periodo</label>
                                        <input type="text" name="periodo" class="form-control" value="{{ $datosGenerales->periodo }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-2">Actualizar Datos Generales</button>
                                    <button type="button" class="btn btn-danger mt-2" id="cancelButton">Cancelar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

            @else
                <!-- Si no existen los datos generales, mostrar el formulario para registrarlos -->
                <div class="card border-primary mb-3">
                    <div class="card-header">Registrar Datos Generales</div>
                    <div class="card-body text-primary">
                        <form action="{{ route('datosGenerales.store') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="nombre_escuela">Nombre de la Escuela</label>
                                <input type="text" name="nombre_escuela" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="ciclo_escolar">Ciclo Escolar</label>
                                <input type="text" name="ciclo_escolar" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="turno">Turno</label>
                                <input type="text" name="turno" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="asignatura">Asignatura</label>
                                <input type="text" name="asignatura" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="grado_grupo">Grado y Grupo</label>
                                <input type="text" name="grado_grupo" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_profesor">Nombre del Profesor</label>
                                <input type="text" name="nombre_profesor" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="periodo">Periodo</label>
                                <input type="text" name="periodo" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success mt-2">Guardar Datos Generales</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Mostrar el formulario de edición cuando se presiona el botón de editar
    document.getElementById('editButton').addEventListener('click', function() {
        document.getElementById('editForm').style.display = 'block';
        this.style.display = 'none'; // Ocultar el botón de editar
    });

    // Ocultar el formulario cuando se presiona el botón de cancelar
    document.getElementById('cancelButton').addEventListener('click', function() {
        document.getElementById('editForm').style.display = 'none';
        document.getElementById('editButton').style.display = 'inline-block'; // Mostrar el botón de editar nuevamente
    });
</script>




<!-- Tarjetas adicionales -->




<div class="container">
  <div class="row">
    <!-- Primera columna -->
    <div class="col-sm-4 mb-3">
      <div class="card border-primary shadow-lg text-center" style="max-width: 18rem;">
        <div class="card-header bg-primary text-white fw-bold">Registrar Alumno/Visualizar</div>
        <div class="card-body text-dark">
          <h5 class="card-title">Control de Registro de Alumnos</h5>
          <p class="card-text">Registrar, editar o eliminar alumnos.</p>
          <a href="{{ route('alumnos.create') }}" class="btn btn-primary fw-bold">Registrar Alumno</a>
        </div>
      </div>
    </div>
    <!-- Segunda columna -->
    <div class="col-sm-4 mb-3">
      <div class="card border-primary shadow-lg text-center" style="max-width: 18rem;">
        <div class="card-header bg-primary text-white fw-bold">Pase de lista</div>
        <div class="card-body text-dark">
          <h5 class="card-title">Control de Asistencias</h5>
          <p class="card-text">Registra las asistencias de los alumnos.</p>
          <a href="{{ route('pase.lista') }}" class="btn btn-primary fw-bold">Pasar Lista</a>
        </div>
      </div>
    </div>
    <!-- Tercera columna -->
    <div class="col-sm-4 mb-3">
      <div class="card border-primary shadow-lg text-center" style="max-width: 18rem;">
        <div class="card-header bg-primary text-white fw-bold">Evaluaciones</div>
        <div class="card-body text-dark">
          <h5 class="card-title">Espacio para Evaluaciones</h5>
          <p class="card-text">Registra y visualiza evaluaciones.</p>
          <a href="{{ route('evaluacion') }}" class="btn btn-primary fw-bold">Ir</a>
        </div>
      </div>
    </div>
  </div>
</div>


  </div>
</div>


</div>
</div>

@endsection
