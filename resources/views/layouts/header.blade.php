<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
</script>
<style>
    /* Media Query para ordenadores con pantallas grandes */
    @media (min-width: 600px) {
        .img-fluid {
            max-height: 25% !important;
            max-width: 25% !important;
        }

        li {
            margin-right: 10px;
        }

    }

    /* Media Query para tablets y móviles */
    @media (max-width: 500px) {
        .img-fluid {
            max-height: 50% !important;
            max-width: 50% !important;
        }

        li {
            margin-bottom: 10px;
        }
    }

    body {
        background-color: #333;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-lg">
    @mobile
        <div class="container-fluid">
            <div class="navbar-brand">
                <img class="img-fluid" src="{{ asset('images/logo' . Request::session()->get('inmobiliaria') . '.png') }}"
                    alt="Logo">
                <button class="navbar-toggler float-end !important" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav justify-content-around w-100 py-2">
                    <li class="nav-item mx-2">
                        @if (Request::is('admin/inmuebles'))
                            <a class="btn btn-md btn-dark text-light d-block w-100 p-2" href="/admin/inmuebles">
                                <i class="fas fa-house"></i>
                                <strong>Inmuebles</strong>
                            </a>
                        @else
                            <a class="btn btn-md btn-outline-dark d-block w-100 p-2" href="/admin/inmuebles">
                                <i class="fas fa-house"></i>
                                <strong>Inmuebles</strong>
                            </a>
                        @endif
                    </li>
                    <li class="nav-item mx-2">
                        @if (Request::is('admin/clientes'))
                            <a class="btn btn-md btn-dark text-light d-block w-100 p-2" href="/admin/clientes">
                                <i class="fas fa-user"></i>
                                <strong>Clientes</strong>
                            </a>
                        @else
                            <a class="btn btn-md btn-outline-dark d-block w-100 p-2" href="/admin/clientes">
                                <i class="fas fa-user"></i>
                                <strong>Clientes</strong>
                            </a>
                        @endif
                    </li>
                    <li class="nav-item mx-2">
                        @if (Request::is('admin/facturacion'))
                            <a class="btn btn-md btn-dark text-light d-block w-100 p-2" href="/admin/facturacion">
                                <i class="fas fa-file-invoice"></i>
                                <strong>Facturación</strong>
                            </a>
                        @else
                            <a class="btn btn-md btn-outline-dark d-block w-100 p-2" href="/admin/facturacion">
                                <i class="fas fa-file-invoice"></i>
                                <strong>Facturación</strong>
                            </a>
                        @endif
                    </li>

                    <li class="nav-item mx-2">
                        @if (Request::is('admin/agenda') || Request::is('home'))
                            <a class="btn btn-md btn-dark text-light d-block w-100 p-2" href="/admin/agenda">
                                <i class="fas fa-book"></i>
                                <strong>Agenda</strong>
                            </a>
                        @else
                            <a class="btn btn-md btn-outline-dark d-block w-100 p-2" href="/admin/agenda">
                                <i class="fas fa-book"></i>
                                <strong>Agenda</strong>
                            </a>
                        @endif
                    </li>
                    @if (Request::session()->get('inmobiliaria') == 'sayco')
                        <li class="ms-auto nav-item mx-2">
                            @if (Request::is('seleccion'))
                                <a class="btn btn-md btn-dark text-light d-block w-100 p-2"
                                    href="{{ route('cambio', ['boton' => 'sancer']) }}">
                                    <i class="fas fa-arrows-rotate"></i>
                                    <strong>Ir a SANCER</strong>
                                </a>
                            @else
                                <a class="btn btn-md btn-outline-dark d-block w-100 p-2"
                                    href="{{ route('cambio', ['boton' => 'sancer']) }}">
                                    <i class="fas fa-arrows-rotate"></i>
                                    <strong>Ir a SANCER</strong>
                                </a>
                            @endif
                        </li>
                    @else
                        <li class="ms-auto nav-item mx-2">
                            @if (Request::is('seleccion'))
                                <a class="btn btn-md btn-dark text-light d-block w-100 p-2"
                                    href="{{ route('cambio', ['boton' => 'sayco']) }}">
                                    <i class="fas fa-arrows-rotate"></i>
                                    <strong>Ir a SAYCO</strong>
                                </a>
                            @else
                                <a class="btn btn-md btn-outline-dark d-block w-100 p-2"
                                    href="{{ route('cambio', ['boton' => 'sayco']) }}">
                                    <i class="fas fa-arrows-rotate"></i>
                                    <strong>Ir a SAYCO</strong>
                                </a>
                            @endif
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    @elsemobile
        <div class="container-fluid col-12">
            <div class="navbar-brand col order-1">
                <img class="img-fluid" src="{{ asset('images/logo' . Request::session()->get('inmobiliaria') . '.png') }}"
                    alt="Logo">
            </div>
            <ul class="navbar-nav me-auto mb-0 mb-lg-0 col order-2">
                <li class="ms-auto nav-item">
                    @if (Request::is('admin/inmuebles'))
                        <a class="btn btn-dark text-light" href="/admin/inmuebles">
                            <i class="fas fa-house"></i>
                            <strong>Inmuebles</strong>
                        </a>
                    @else
                        <a class="btn btn-outline-dark" href="/admin/inmuebles">
                            <i class="fas fa-house"></i>
                            <strong>Inmuebles</strong>
                        </a>
                    @endif

                </li>
                <li class="nav-item">
                    @if (Request::is('admin/clientes'))
                        <a class="btn btn-dark text-light" href="/admin/clientes">
                            <i class="fas fa-user"></i>
                            <strong>Clientes</strong>
                        </a>
                    @else
                        <a class="btn btn-outline-dark" href="/admin/clientes">
                            <i class="fas fa-user"></i>
                            <strong>Clientes</strong>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (Request::is('admin/facturacion'))
                        <a class="btn btn-dark text-light" href="/admin/facturacion">
                            <i class="fas fa-file-invoice"></i>
                            <strong>Facturación</strong>
                        </a>
                    @else
                        <a class="btn btn-outline-dark" href="/admin/facturacion">
                            <i class="fas fa-file-invoice"></i>
                            <strong>Facturación</strong>
                        </a>
                    @endif

                </li>
                <li class="nav-item">
                    @if (Request::is('admin/agenda') || Request::is('home'))
                        <a class="btn btn-dark text-light" href="/admin/agenda">
                            <i class="fas fa-book"></i>
                            <strong>Agenda</strong>
                        </a>
                    @else
                        <a class="btn btn-outline-dark" href="/admin/agenda">
                            <i class="fas fa-book"></i>
                            <strong>Agenda</strong>
                        </a>
                    @endif
                </li>
                @if (Request::session()->get('inmobiliaria') == 'sayco')
                    <li class="ms-auto nav-item">
                        @if (Request::is('seleccion'))
                            <a class="btn btn-dark text-light" href="{{ route('cambio', ['boton' => 'sancer']) }}">
                                <i class="fas fa-arrows-rotate"></i>
                                <strong>Ir a SANCER</strong>
                            </a>
                        @else
                            <a class="btn btn-outline-dark" href="{{ route('cambio', ['boton' => 'sancer']) }}">
                                <i class="fas fa-arrows-rotate"></i>
                                <strong>Ir a SANCER</strong>
                            </a>
                        @endif
                    </li>
                @else
                    <li class="ms-auto nav-item">
                        @if (Request::is('seleccion'))
                            <a class="btn btn-dark text-light" href="{{ route('cambio', ['boton' => 'sayco']) }}">
                                <i class="fas fa-arrows-rotate"></i>
                                <strong>Ir a SAYCO</strong>
                            </a>
                        @else
                            <a class="btn btn-outline-dark" href="{{ route('cambio', ['boton' => 'sayco']) }}">
                                <i class="fas fa-arrows-rotate"></i>
                                <strong>Ir a SAYCO</strong>
                            </a>
                        @endif
                    </li>
                @endif
            </ul>
        </div>
        </div>
    @endmobile
</nav>
