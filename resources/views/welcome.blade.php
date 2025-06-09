@extends('layouts.app')

@section('title', 'Portada - Sistema de Gestión Escolar')

@section('content')
    <!-- Hero principal -->
    <div class="container-fluid bg-primary text-white text-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <h1 class="display-4 font-weight-bold">Bienvenido al Sistema de Gestión Escolar</h1>
                    <p class="lead">Administra alumnos, calificaciones y reportes de forma eficiente.</p>

                    <!-- Botones de acción -->
                    <div class="mt-4 d-flex flex-column flex-md-row justify-content-center gap-3">
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg {{ Request::is('login') ? 'active shadow' : '' }}">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg {{ Request::is('register') ? 'active shadow' : '' }}">
                            Regístrate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Informativa -->
    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="text-primary">¿Qué es el Sistema de Gestión Escolar?</h2>
                <p class="lead">Este sistema permite a los administradores y docentes llevar un control completo de los alumnos, calificaciones, y reportes. Ofrece una interfaz intuitiva y moderna para facilitar la gestión académica.</p>
            </div>
            <div class="col-md-6">
                <p class="lead">Aprovecha las herramientas disponibles para un control académico eficiente y sin complicaciones, accesible desde cualquier dispositivo.</p>
            </div>
        </div>
    </div>

    <!-- Sección de Beneficios -->
    <div class="container mt-5">
        <h2 class="text-center text-primary mb-4">Beneficios de Usar el Sistema</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card border-primary h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Eficiencia</h5>
                        <p class="card-text">Centraliza la información y reduce el tiempo de gestión académica.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-success h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Fácil de Usar</h5>
                        <p class="card-text">Diseñado con una interfaz clara y amigable para todos los usuarios.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-warning h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Accesibilidad</h5>
                        <p class="card-text">Disponible desde cualquier lugar y dispositivo con conexión a internet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
