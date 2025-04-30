@extends('layouts.app')

@section('title', 'Portada - Sistema de Gestión Escolar')

@section('content')
    <!-- Sección de Portada -->
    <div class="container-fluid bg-primary text-white text-center py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-4 font-weight-bold">Bienvenido al Sistema de Gestión Escolar</h1>
                <p class="lead">Administra el control de alumnos, calificaciones y reportes de manera eficiente.</p>
            </div>
        </div>
    </div>

    <!-- Sección de Funcionalidades Destacadas -->
    <div class="container mt-5">
        <div class="row">
            <!-- Control de Alumnos -->
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Control de Alumnos</h5>
                        <p class="card-text">Registra y administra la información de los alumnos en un solo lugar. Fácil acceso y edición.</p>
                    </div>
                </div>
            </div>

            <!-- Gestión de Calificaciones -->
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Gestión de Calificaciones</h5>
                        <p class="card-text">Administra las calificaciones de los alumnos, realiza cálculos automáticos y visualiza el desempeño académico.</p>
                    </div>
                </div>
            </div>

            <!-- Generación de Reportes -->
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Generación de Reportes</h5>
                        <p class="card-text">Genera reportes detallados sobre el desempeño de los alumnos, filtrados por grado, grupo y calificación.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Informativa -->
        <div class="row mt-5">
            <div class="col-md-6">
                <h2 class="text-primary">¿Qué es el Sistema de Gestión Escolar?</h2>
                <p class="lead">Este sistema está diseñado para ayudar a los administradores y maestros a llevar un control completo de los alumnos, registrar calificaciones, realizar seguimientos y generar reportes en tiempo real. A través de una interfaz sencilla y visualmente atractiva, los usuarios podrán gestionar toda la información necesaria con facilidad.</p>
            </div>
            <div class="col-md-6">
                <p class="lead">Aprovecha todas las herramientas disponibles para un control académico eficiente y sin complicaciones.</p>
            </div>
        </div>
    </div>

    <!-- Sección de Beneficios -->
    <div class="container mt-5">
        <h2 class="text-center text-primary mb-4">Beneficios de Usar el Sistema</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Eficiencia</h5>
                        <p class="card-text">Ahorra tiempo al tener toda la información centralizada y accesible desde cualquier lugar.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title">Fácil de Usar</h5>
                        <p class="card-text">Una interfaz intuitiva y amigable para que los usuarios no tengan complicaciones al navegar.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5 class="card-title">Accesibilidad</h5>
                        <p class="card-text">Accede a la información en cualquier momento y desde cualquier dispositivo con acceso a internet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
