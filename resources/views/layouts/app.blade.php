<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel App')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Vincula tu archivo CSS para el footer -->
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <!-- Bootstrap Icons para los íconos de redes sociales -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <header>
        <!-- Navbar global -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <!-- Logo a la izquierda -->
                <a href="{{ url('/home') }}" class="navbar-brand">
                    <img src="{{ asset('Prueba.png') }}" alt="Logo" style="height: 60px;">
                </a>

                <!-- Botón para móvil -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Enlaces a la derecha -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <!-- Enlace a Login y Register si el usuario no está autenticado -->
                        @guest
                            <li class="nav-item {{ Request::is('login') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('login') }}">Iniciar sesion</a>
                            </li>
                            <li class="nav-item {{ Request::is('register') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('register') }}">Registrate</a>
                            </li>
                        @else
                            <!-- Muestra el nombre del usuario autenticado y un menú de logout -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Salir
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-4 flex-grow-1">
        @yield('content') <!-- Aquí se inyectará el contenido de la vista login o registro -->
    </main>

    <footer class="bg-light text-center text-lg-start mt-4 py-4">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Acerca de</h5>
                    <p>Información sobre la aplicación.</p>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Enlaces rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark">Inicio</a></li>
                        <li><a href="#" class="text-dark">Características</a></li>
                        <li><a href="#" class="text-dark">Precios</a></li>
                        <li><a href="#" class="text-dark">Contáctanos</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Síguenos</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark"><i class="bi bi-facebook"></i> Facebook</a></li>
                        <li><a href="#" class="text-dark"><i class="bi bi-twitter"></i> Twitter</a></li>
                        <li><a href="#" class="text-dark"><i class="bi bi-linkedin"></i> LinkedIn</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="text-center p-3 bg-dark text-white">
            <p>&copy; {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
