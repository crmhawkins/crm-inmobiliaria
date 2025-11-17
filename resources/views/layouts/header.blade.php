<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
</script>
<style>
    :root {
        --corporate-green: #6b8e6b;
        --corporate-green-dark: #5a7c5a;
        --corporate-green-light: #7fa07f;
        --corporate-green-lightest: #e8f0e8;
    }

    .navbar-custom {
        background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%) !important;
        box-shadow: 0 4px 15px rgba(107, 142, 107, 0.3);
        padding: 15px 0;
    }

    .navbar-brand img {
        max-height: 50px;
        filter: brightness(0) invert(1);
        transition: transform 0.3s ease;
    }

    .navbar-brand img:hover {
        transform: scale(1.05);
    }

    .nav-btn {
        color: white !important;
        border: 2px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        margin: 0 5px;
        transition: all 0.3s ease;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.1);
    }

    .nav-btn:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        border-color: rgba(255, 255, 255, 0.5) !important;
        transform: translateY(-2px);
        color: white !important;
    }

    .nav-btn.active {
        background: rgba(255, 255, 255, 0.25) !important;
        border-color: white !important;
        box-shadow: 0 4px 10px rgba(255, 255, 255, 0.2);
    }

    .navbar-toggler {
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Media Query para ordenadores con pantallas grandes */
    @media (min-width: 600px) {
        .img-fluid {
            max-height: 50px !important;
        }
    }

    /* Media Query para tablets y móviles */
    @media (max-width: 500px) {
        .img-fluid {
            max-height: 40px !important;
        }

        .nav-btn {
            margin: 5px 0;
            width: 100%;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-custom">
    @mobile
        <div class="container-fluid">
            <div class="navbar-brand">
                <img class="img-fluid" src="{{ asset('images/logo' . Request::session()->get('inmobiliaria') . '.png') }}"
                    alt="Logo">
                <button class="navbar-toggler float-end" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav justify-content-around w-100 py-2">
                    <li class="nav-item">
                        <a class="nav-btn d-block w-100 {{ Request::is('admin/inmuebles') ? 'active' : '' }}" href="/admin/inmuebles">
                            <i class="fas fa-house me-2"></i>
                            <strong>Inmuebles</strong>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-btn d-block w-100 {{ Request::is('admin/clientes') ? 'active' : '' }}" href="/admin/clientes">
                            <i class="fas fa-user me-2"></i>
                            <strong>Clientes</strong>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-btn d-block w-100 {{ Request::is('admin/facturacion') ? 'active' : '' }}" href="/admin/facturacion">
                            <i class="fas fa-file-invoice me-2"></i>
                            <strong>Facturación</strong>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-btn d-block w-100 {{ Request::is('admin/agenda') || Request::is('home') ? 'active' : '' }}" href="/admin/agenda">
                            <i class="fas fa-book me-2"></i>
                            <strong>Agenda</strong>
                        </a>
                    </li>
                    @if (Request::session()->get('inmobiliaria') == 'sayco')
                        <li class="nav-item">
                            <a class="nav-btn d-block w-100 {{ Request::is('seleccion') ? 'active' : '' }}"
                                href="{{ route('cambio', ['boton' => 'sancer']) }}">
                                <i class="fas fa-arrows-rotate me-2"></i>
                                <strong>Ir a SANCER</strong>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-btn d-block w-100 {{ Request::is('seleccion') ? 'active' : '' }}"
                                href="{{ route('cambio', ['boton' => 'sayco']) }}">
                                <i class="fas fa-arrows-rotate me-2"></i>
                                <strong>Ir a SAYCO</strong>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    @elsemobile
        <div class="container-fluid">
            <div class="navbar-brand">
                <img class="img-fluid" src="{{ asset('images/logo' . Request::session()->get('inmobiliaria') . '.png') }}"
                    alt="Logo">
            </div>
            <ul class="navbar-nav ms-auto mb-0 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-btn {{ Request::is('admin/inmuebles') ? 'active' : '' }}" href="/admin/inmuebles">
                        <i class="fas fa-house me-2"></i>
                        <strong>Inmuebles</strong>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-btn {{ Request::is('admin/clientes') ? 'active' : '' }}" href="/admin/clientes">
                        <i class="fas fa-user me-2"></i>
                        <strong>Clientes</strong>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-btn {{ Request::is('admin/facturacion') ? 'active' : '' }}" href="/admin/facturacion">
                        <i class="fas fa-file-invoice me-2"></i>
                        <strong>Facturación</strong>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-btn {{ Request::is('admin/agenda') || Request::is('home') ? 'active' : '' }}" href="/admin/agenda">
                        <i class="fas fa-book me-2"></i>
                        <strong>Agenda</strong>
                    </a>
                </li>
                @if (Request::session()->get('inmobiliaria') == 'sayco')
                    <li class="nav-item">
                        <a class="nav-btn {{ Request::is('seleccion') ? 'active' : '' }}" href="{{ route('cambio', ['boton' => 'sancer']) }}">
                            <i class="fas fa-arrows-rotate me-2"></i>
                            <strong>Ir a SANCER</strong>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-btn {{ Request::is('seleccion') ? 'active' : '' }}" href="{{ route('cambio', ['boton' => 'sayco']) }}">
                            <i class="fas fa-arrows-rotate me-2"></i>
                            <strong>Ir a SAYCO</strong>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    @endmobile
</nav>
