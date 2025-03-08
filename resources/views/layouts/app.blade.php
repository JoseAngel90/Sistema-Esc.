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

    <!-- AOS Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <style>
        /* Estilos para el preloader */
        #preloader {
            position: fixed;
            width: 100%;
            height: 100%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #3498db;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner"></div>
    </div>

    <header>
        <!-- Navbar global -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
            <div class="container-fluid">
                
                    <img src="{{ asset('Prueba.png') }}" alt="Logo" style="height: 60px;">
                

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item {{ Request::is('login') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                            </li>
                            <li class="nav-item {{ Request::is('register') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('register') }}">Regístrate</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
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
        <div data-aos="fade-up" data-aos-duration="1000">
            @yield('content')
        </div>
    </main>

    <footer class="bg-light text-center text-lg-start mt-4 py-4" id="footer">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-right">
                    <h5>Acerca de</h5>
                    <p>Información sobre la aplicación.</p>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="zoom-in">
                    <h5>Enlaces rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark">Inicio</a></li>
                        <li><a href="#" class="text-dark">Características</a></li>
                        <li><a href="#" class="text-dark">Precios</a></li>
                        <li><a href="#" class="text-dark">Contáctanos</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-left">
                    <h5>Síguenos</h5>
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

    <!-- AOS Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
    </script>

    <!-- GSAP para animaciones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        window.onload = function() {
            // Ocultar el preloader con una animación suave
            gsap.to("#preloader", { opacity: 0, duration: 1, onComplete: function() {
                document.getElementById("preloader").style.display = "none";
            }});

            // Navbar animación de entrada
            gsap.from("#navbar", { duration: 1, y: -50, opacity: 0, ease: "power2.out" });

            // Footer animación de entrada
            gsap.from("#footer", { duration: 1.5, y: 50, opacity: 0, ease: "power2.out" });
        };
    </script>

</body>
</html>
