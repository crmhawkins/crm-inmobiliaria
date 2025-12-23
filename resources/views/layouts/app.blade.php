<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield('title') </title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.css"
        integrity="sha512-CaTMQoJ49k4vw9XO0VpTBpmMz8XpCWP5JhGmBvuBqCOaOHWENWO1CrVl09u4yp8yBVSID6smD4+gpzDJVQOPwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="//cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">

    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <x-livewire-alert::scripts />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/corporate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #2d3748;
        }

        :root {
            --corporate-green: #6b8e6b;
            --corporate-green-dark: #5a7c5a;
            --corporate-green-light: #7fa07f;
            --corporate-green-lightest: #e8f0e8;
        }

        .page-header {
            background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%);
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.2);
        }

        .page-header h1 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            padding: 0;
            border: none !important;
        }

        .page-header h2 {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            font-weight: 400;
            margin: 8px 0 0 0;
            padding: 0;
        }

        .content-wrapper {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            min-height: calc(100vh - 200px);
        }

        /* Responsive para móvil y tablet */
        @media (max-width: 768px) {
            .page-header {
                padding: 20px 0;
                margin-bottom: 20px;
                border-radius: 0 0 15px 15px;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .page-header h2 {
                font-size: 0.95rem;
            }

            .content-wrapper {
                padding: 15px;
                border-radius: 12px;
                min-height: auto;
            }

            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }

            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                padding: 15px 0;
                margin-bottom: 15px;
            }

            .page-header h1 {
                font-size: 1.25rem;
            }

            .page-header h2 {
                font-size: 0.85rem;
            }

            .content-wrapper {
                padding: 12px;
                border-radius: 10px;
            }
        }

        /* Mejoras para touch devices */
        @media (hover: none) and (pointer: coarse) {
            button, .btn, a {
                min-height: 44px;
                min-width: 44px;
            }

            input, select, textarea {
                font-size: 16px; /* Previene zoom en iOS */
            }
        }

        @if (Request::session()->get('inmobiliaria') == 'sayco')
            .page-wrapper {
                background-color: #f8f9fa !important;
            }
        @else
            .page-wrapper {
                background-color: #f8f9fa !important;
            }
        @endif
    </style>

    @yield('head')
</head>

<body>
    @php
        $user = Auth::user();
    @endphp
    <div id="app">
        @include('layouts.header')
        <div class="page-wrapper chiller-theme toggled">
            <div class="container-fluid py-4">
                <div class="page-header">
                    <div class="container">
                        <h1>@yield('encabezado', 'Dashboard')</h1>
                        <h2>@yield('subtitulo', 'Resumen general')</h2>
                    </div>
                </div>
                <div class="container">
                    <div class="content-wrapper">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.js"
        integrity="sha512-f8kZwYACKF8unHuRV7j/5ILZfflRncxHp1f6y/PKuuRpCVgpORNZMne1jrghNzTVlXabUXIg1iJ5PvhuAaau6Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <!-- Interceptar Bootstrap ANTES de que se cargue -->
    <script>
    (function() {
        'use strict';

        // Función para eliminar elementos de Bootstrap inmediatamente
        function killBootstrapNavbar() {
            // Eliminar TODOS los elementos con estas clases/ids, PERO NO los nuestros
            const selectors = [
                '.navbar-collapse',
                '#navbarSupportedContent',
                '[class*="navbar-collapse"]',
                '[id*="navbarSupported"]'
            ];

            selectors.forEach(function(selector) {
                try {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(function(el) {
                        if (el && el.parentNode) {
                            // NO eliminar si está dentro de nuestros elementos personalizados
                            if (!el.closest('.main-header') &&
                                !el.closest('.mobile-overlay') &&
                                !el.closest('.header-menu-desktop')) {
                                try {
                                    el.remove();
                                } catch(e) {
                                    el.outerHTML = '';
                                }
                            }
                        }
                    });
                } catch(e) {}
            });
        }

        // Ejecutar inmediatamente
        killBootstrapNavbar();

        // Ejecutar cada 10ms para ser más agresivo
        setInterval(killBootstrapNavbar, 10);

        // Observar cambios en el DOM
        const observer = new MutationObserver(function(mutations) {
            let shouldKill = false;
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.classList) {
                            if (node.classList.contains('navbar-collapse') ||
                                node.classList.contains('collapse') ||
                                node.id === 'navbarSupportedContent') {
                                shouldKill = true;
                            }
                        }
                        if (node.id && node.id.includes('navbar')) {
                            shouldKill = true;
                        }
                    }
                });
            });
            if (shouldKill) {
                killBootstrapNavbar();
            }
        });

        if (document.body) {
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class', 'id']
            });
        }

        // Interceptar cuando Bootstrap se carga
        window.addEventListener('load', function() {
            killBootstrapNavbar();
            setInterval(killBootstrapNavbar, 10);
        });

        // Interceptar DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                killBootstrapNavbar();
                setInterval(killBootstrapNavbar, 10);
            });
        }
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <!-- Interceptar Bootstrap después de que se carga -->
    <script>
    (function() {
        'use strict';

        // Sobrescribir la función de inicialización de Collapse de Bootstrap
        if (typeof window.bootstrap !== 'undefined' && window.bootstrap.Collapse) {
            const OriginalCollapse = window.bootstrap.Collapse;
            window.bootstrap.Collapse = function(element, config) {
                // Si el elemento tiene clases de navbar, NO inicializar
                if (element) {
                    const hasNavbarClass = element.classList && (
                        element.classList.contains('navbar-collapse') ||
                        element.id === 'navbarSupportedContent'
                    );
                    if (hasNavbarClass) {
                        return null;
                    }
                }
                return new OriginalCollapse(element, config);
            };
        }

        // Función para eliminar elementos
        function killBootstrapNavbar() {
            const selectors = [
                '.navbar-collapse',
                '#navbarSupportedContent',
                '[class*="navbar-collapse"]',
                '[id*="navbarSupported"]',
                'nav[class*="collapse"]'
            ];

            selectors.forEach(function(selector) {
                try {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(function(el) {
                        if (el && el.parentNode) {
                            try {
                                el.remove();
                            } catch(e) {
                                el.outerHTML = '';
                            }
                        }
                    });
                } catch(e) {}
            });
        }

        killBootstrapNavbar();
        setInterval(killBootstrapNavbar, 10);
    })();
    </script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.bootstrap5.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.css" rel="stylesheet" />

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.js"></script>
    @livewireScripts
    @yield('scripts')

</body>

</html>
