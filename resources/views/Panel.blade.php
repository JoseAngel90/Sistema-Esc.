@extends('layouts.app')

@section('content')
<style>
    /* Animaciones de entrada */
    .card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeIn 0.8s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Efecto hover */
    .card:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    /* Animación del botón */
    .btn-danger:hover {
        transform: scale(1.1);
        transition: transform 0.2s ease-in-out;
    }
</style>

<div class="container">
    <!-- Información del usuario autenticado -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h4>Bienvenido, {{ $usuario->name }}</h4>
            <p><strong>Correo electrónico:</strong> {{ $usuario->email }}</p>
        </div>
    </div>

    <div class="row">
        <!-- Tarjeta 1 -->
        <div class="col-sm-4 mb-3">
            <div class="card border-primary shadow-lg text-center h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-user-plus"></i> Registrar Alumno/Visualizar
                </div>
                <div class="card-body text-dark">
                    <h5 class="card-title">Control de Registro de Alumnos</h5>
                    <p class="card-text">Registrar, editar o eliminar alumnos.</p>
                    <a href="{{ route('alumnos.create', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-primary fw-bold">Ir</a>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta 2 -->
        <div class="col-sm-4 mb-3">
            <div class="card border-success shadow-lg text-center h-100">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fas fa-check-circle"></i> Pase de lista
                </div>
                <div class="card-body text-dark">
                    <h5 class="card-title">Control de Asistencias</h5>
                    <p class="card-text">Registra las asistencias de los alumnos.</p>
                    @if ($existenAlumnos)
                        <a href="{{ route('pase.lista', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-success fw-bold text-white">
                            <i class="fas fa-arrow-right"></i> Ir
                        </a>
                    @else
                        <p class="text-danger">No hay alumnos registrados en este grado y grupo.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Tarjeta 3 -->
        <div class="col-sm-4 mb-3">
            <div class="card border-warning shadow-lg text-center h-100">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-chart-line"></i> Evaluaciones
                </div>
                <div class="card-body text-dark">
                    <h5 class="card-title">Espacio para Evaluaciones</h5>
                    <p class="card-text">Registra y visualiza evaluaciones.</p>
                    @if ($existenAlumnos)
                        <a href="{{ route('evaluacion', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-warning fw-bold text-dark">
                            <i class="fas fa-arrow-right"></i> Ir
                        </a>
                    @else
                        <p class="text-danger">No hay alumnos registrados en este grado y grupo.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tarjeta 4 -->
        <div class="col-sm-4 mb-3">
            <div class="card border-info shadow-lg text-center h-100">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-file-alt"></i> Examen Diagnóstico
                </div>
                <div class="card-body text-dark">
                    <h5 class="card-title">Espacio para Examen Diagnóstico</h5>
                    <p class="card-text">Registra y visualiza exámenes diagnósticos.</p>
                    @if ($existenAlumnos)
                        <a href="{{ route('diagnostico', ['grado' => $grado, 'grupo' => $grupo]) }}" class="btn btn-info fw-bold text-white">
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
