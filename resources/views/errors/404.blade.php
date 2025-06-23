@extends('layouts.app')

@section('content')
<div class="container mt-5 text-center">
    <h1 class="display-1 fw-bold text-danger">404</h1>
    <p class="lead fw-semibold text-dark mb-4" style="font-size: 1.5rem;">
        ¡Oops! Página no encontrada.
    </p>
    <p class="text-muted mb-4">
        La página que estás buscando no existe o fue movida.
    </p>

    <a href="{{ url('/home') }}" class="btn btn-primary btn-lg" style="border-radius: 25px; padding: 12px 30px; font-size: 1.2rem;">
        <i class="bi bi-house-door-fill"></i> Volver al Inicio
    </a>
</div>
@endsection

<style>
    body {
        background-color: #f7f7f7;
        font-family: 'Roboto', sans-serif;
    }

    .container {
        padding: 50px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        max-width: 600px;
    }

    h1 {
        font-size: 8rem;
    }

    .btn:hover {
        transform: scale(1.05);
        transition: 0.2s ease;
    }
</style>

