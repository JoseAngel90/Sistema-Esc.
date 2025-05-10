@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        @if (session('newPassword'))
            <br>
            <strong>Nueva contraseña:</strong> {{ session('newPassword') }}
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            {{ __('Bienvenido Administrador') }}
        </div>

        <div class="card-body">
            <p>{{ __('Desde aquí podrás administrar a los usuarios') }}</p>
            <p>{{ __('Puedes ver los usuarios registrados, actualizar la contraseña, agregar un nuevo administrador.') }}</p>
            <p>{{ __('Si necesitas ayuda, contacta con el soporte técnico') }}</p>

            <h5 class="mt-4">Usuarios Registrados</h5>
            @if($users->isEmpty())
                <p>No hay usuarios registrados.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->id ? 'Activo' : 'En espera' }}</td>
                                    <td>{{ $user->roles == 'admin' ? 'Administrador' : 'Usuario' }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <!-- Restablecer -->
                                            <form action="{{ route('users.resetPassword', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning btn-sm">
                                                    <i class="bi bi-key"></i> Restablecer
                                                </button>
                                            </form>
                                            <!-- Eliminar -->
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>                             
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <h5 class="mt-4">Usuarios Pendientes</h5>
            @if($pendingUsers->isEmpty())
                <p>No hay usuarios pendientes.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUsers as $pendingUser)
                                <tr>
                                    <td>{{ $pendingUser->name }}</td>
                                    <td>{{ $pendingUser->email }}</td>
                                    <td>
                                        <form action="{{ route('users.approve', $pendingUser->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Aprobar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card-footer text-muted">
            {{ __('Última actualización: :date', ['date' => now()->format('d/m/Y H:i')]) }}<br>
            {{ __('Versión: 1.0.0') }}
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            {{ __('Registrar Nuevo Administrador') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('administradores.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">{{ __('Nombre') }}</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">{{ __('Correo Electrónico') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">{{ __('Registrar Administrador') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
