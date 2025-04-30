@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h4>Iniciar sesión</h4>
                </div>
                <div class="card-body">
                    <!-- Formulario de inicio de sesión -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="email">Correo electrónico</label>
                            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary fw-bold px-4">Iniciar sesión</button>
                        </div>

                        <!-- Botón de recuperación de contraseña -->
                        <div class="text-center mt-3">
                            <a href="{{ route('password.request') }}" class="btn btn-link text-danger fw-bold">¿Olvidaste tu contraseña?</a>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
