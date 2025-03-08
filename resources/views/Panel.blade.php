@extends('layouts.app')

@section('content')
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
          <a href="{{ route('alumnos.create', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-primary fw-bold">Registrar Alumno</a>
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
          @if ($existenAlumnos)
                <a href="{{ route('pase.lista', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-primary fw-bold text-white">
                    <i class="fas fa-arrow-right"></i> Ir
                </a>
            @else
                <p class="text-danger">No hay alumnos registrados en este grado y grupo.</p>
            @endif
        </div>
      </div>
    </div>
    <!-- Tarjeta 3: Evaluaciones -->
<div class="col-sm-4 mb-3">
    <div class="card border-primary shadow-lg text-center" style="max-width: 18rem;">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fas fa-chart-line"></i> Evaluaciones
        </div>
        <div class="card-body text-dark">
            <h5 class="card-title">Espacio para Evaluaciones</h5>
            <p class="card-text">Registra y visualiza evaluaciones.</p>
            
            @if ($existenAlumnos)
                <a href="{{ route('evaluacion', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-primary fw-bold text-white">
                    <i class="fas fa-arrow-right"></i> Ir
                </a>
            @else
                <p class="text-danger">No hay alumnos registrados en este grado y grupo.</p>
            @endif
        </div>
    </div>
</div>
  </div>
  <!-- Botón para regresar al home -->
  <div class="text-center mt-4">
    <a href="{{ route('home') }}" class="btn btn-danger fw-bold" onclick="return confirm('¿Seguro que quieres salir?')">Regresar al Home</a>
  </div>
</div>

@endsection