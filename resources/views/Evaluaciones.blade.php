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

    <!-- Título centrado con tipografía moderna -->
    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold" style="font-family: 'Roboto', sans-serif; font-size: 2rem; letter-spacing: 2px;">Evaluación</h2>
    </div>

    <!-- Botón de acción con estilo minimalista utilizando Bootstrap -->
    <div class="mb-4 text-center">
        <a id="btnIr" href="{{ route('CalificarCotejo', ['grado' => request('grado'), 'grupo' => request('grupo')]) }}" class="btn btn-primary btn-lg" style="border-radius: 25px; padding: 12px 30px; font-size: 1.2rem;">
            IR A CALIFICAR COTEJO
        </a>
    </div>

    <!-- Botón para regresar con un diseño simple pero elegante -->
    <div class="text-center">
    <a href="{{ route('panel', ['grado' => $gradoFiltro, 'grupo' => $grupoFiltro]) }}" class="btn btn-secondary btn-lg" style="border-radius: 25px; padding: 12px 30px; font-size: 1.2rem;">
        <i class="bi bi-arrow-left-circle"></i> Regresar al Panel
    </a>
</div>


</div>

<!-- Estilos adicionales para mejorar la estética -->
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f7f7f7;
        color: #333;
    }
    .container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }
    .btn {
        transition: transform 0.2s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .alert {
        font-size: 1.1rem;
        border-radius: 10px;
        padding: 15px;
    }
    .alert-success {
        background-color: #e8f9e8;
        color: #4CAF50;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #d9534f;
    }
</style>
@endsection
