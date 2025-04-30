<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel App')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <style>
        /* Agregar estos estilos en tu archivo CSS para personalizar las animaciones */
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

        .writing-loader {
            display: flex;
            align-items: center;
            font-family: 'Courier New', Courier, monospace;
        }

        .writing-loader .text {
            font-size: 1.6rem;
            white-space: nowrap;
            overflow: hidden;
            border-right: 2px solid #000;
            width: 0;
            animation: typing 1.2s steps(12, end) forwards, blink 0.6s step-end infinite;
        }

        .writing-loader .pencil {
            font-size: 2rem;
            margin-left: 10px;
            animation: writeMove 1.2s ease-in-out infinite;
        }

        @keyframes typing {
            from {
                width: 0;
            }

            to {
                width: 10ch;
            }
        }

        @keyframes blink {
            50% {
                border-color: transparent;
            }
        }

        @keyframes writeMove {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(3px);
            }

            50% {
                transform: translateX(0);
            }

            75% {
                transform: translateX(-3px);
            }

            100% {
                transform: translateX(0);
            }
        }

        /* Asegurarse de que el logo en el navbar sea responsivo */
        #navbar img {
            max-height: 60px;
            width: auto;
        }

        /* Estilo del footer para hacerlo más responsivo */
        #footer .container {
            padding-left: 15px;
            padding-right: 15px;
        }

        @media (max-width: 576px) {
            .writing-loader .text {
                font-size: 1.4rem;
            }

            .writing-loader .pencil {
                font-size: 1.6rem;
            }

            #footer h5 {
                font-size: 1.2rem;
            }

            #footer p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Preloader nuevo -->
    <div id="preloader">
        <div class="writing-loader">
            <div class="text">Cargando...</div>
            <div class="pencil">✏️</div>
        </div>
    </div>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
            <div class="container-fluid">
                <img src="{{ asset('Prueba.png') }}" alt="Logo" class="img-fluid">
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
                                    <a class="dropdown-item" href="{{ route('logout') }} "
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

    <main class="container mt-4 flex-grow-1 position-relative">
        <div data-aos="fade-up" data-aos-duration="1000">
            @yield('content')
        </div>
    </main>

    <footer class="bg-light text-center text-lg-start mt-4 py-4" id="footer">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-right">
                    <h5>Acerca de</h5>
                    <p>Esta aplicación fue desarrollada para facilitar la gestión de alumnos, calificaciones y reportes académicos. Ofrece un control sencillo y accesible para los administradores y educadores.</p>
                    <p>Contamos con un sistema de gestión integral que permite mantener un seguimiento adecuado del rendimiento académico de los estudiantes, de forma eficiente y segura.</p>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-left">
                    <h5>Contacto</h5>
                    <p>Si tienes alguna pregunta o sugerencia, no dudes en ponerte en contacto con nosotros. Estamos aquí para ayudarte.</p>
                    <p><strong>Email:</strong> ruy.hernandez@gruposyscom.com.mx</p>
                    <p><strong>Teléfono:</strong> 246-238-4111</p>
                    <p><strong>Dirección:</strong> 65 Sucursal chiautempan: Manuel Saldaña Mte. No.10, Col. Centro Santa Ana Chiautempan Tlax.</p>
                </div>
            </div>
        </div>

        <div class="text-center p-3 bg-dark text-white">
            <p>© {{ date('Y') }} Mi Aplicación. Todos los derechos reservados.</p>
            <p>Somos un equipo comprometido con el desarrollo de soluciones educativas de calidad.</p>
        </div>
    </footer>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <script>
        // Iniciar AOS para animaciones
        AOS.init();

        // Mostrar el preloader durante 1 segundo y luego ocultarlo
        window.onload = function () {
            setTimeout(() => {
                document.getElementById('preloader').style.transition = "opacity 0.5s ease, visibility 0.5s ease";
                document.getElementById('preloader').style.opacity = "0";
                document.getElementById('preloader').style.visibility = "hidden";
            }, 500);
        };

        // Animación de Navbar y Footer con GSAP
        gsap.from("#navbar", {
            duration: 1,
            y: -50,
            opacity: 0,
            ease: "power2.out"
        });

        gsap.from("#footer", {
            duration: 1.5,
            y: 50,
            opacity: 0,
            ease: "power2.out"
        });
    </script>

</body>
</html>
