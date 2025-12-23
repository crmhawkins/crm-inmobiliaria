<style>
    :root {
        --corporate-green: #6b8e6b;
        --corporate-green-dark: #5a7c5a;
    }

    .main-header {
        background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%);
        box-shadow: 0 4px 15px rgba(107, 142, 107, 0.3);
        padding: 15px 0;
        width: 100%;
        position: relative;
        z-index: 1000;
    }

    .header-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .header-logo img {
        max-height: 50px;
        filter: brightness(0) invert(1);
        transition: transform 0.3s ease;
    }

    .header-logo img:hover {
        transform: scale(1.05);
    }

    .header-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .header-nav-link {
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        padding: 8px 16px;
        margin: 0 5px;
        transition: all 0.3s ease;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .header-nav-link:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        color: white;
    }

    .header-nav-link.active {
        background: rgba(255, 255, 255, 0.25);
        border-color: white;
        box-shadow: 0 4px 10px rgba(255, 255, 255, 0.2);
    }

    .mobile-menu-toggle {
        display: none;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        padding: 10px;
        color: white;
        cursor: pointer;
        min-width: 44px;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 5px;
    }

    .mobile-menu-toggle:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .mobile-menu-toggle span {
        display: block;
        width: 24px;
        height: 2px;
        background: white;
        border-radius: 2px;
        transition: all 0.3s;
    }

    .mobile-menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99998;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }

    .mobile-menu-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .mobile-menu-panel {
        position: fixed;
        top: 0;
        right: 0;
        width: 85%;
        max-width: 350px;
        height: 100%;
        background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%);
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.3);
        z-index: 99999;
        transform: translateX(100%);
        transition: transform 0.3s;
        overflow-y: auto;
    }

    .mobile-menu-overlay.active .mobile-menu-panel {
        transform: translateX(0);
    }

    .mobile-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .mobile-menu-header h5 {
        color: white;
        margin: 0;
        font-weight: 600;
    }

    .mobile-menu-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-menu-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .mobile-menu-body {
        padding: 20px 0;
    }

    .mobile-menu-link {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        color: white;
        text-decoration: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: background 0.3s;
    }

    .mobile-menu-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .mobile-menu-link.active {
        background: rgba(255, 255, 255, 0.2);
        border-left: 4px solid white;
    }

    .mobile-menu-link i {
        width: 24px;
        margin-right: 12px;
        text-align: center;
    }

    @media (min-width: 992px) {
        .header-nav {
            display: flex;
        }
        .mobile-menu-toggle {
            display: none;
        }
        .mobile-menu-overlay {
            display: none !important;
        }
    }

    @media (max-width: 991px) {
        .header-nav {
            display: none;
        }
        .mobile-menu-toggle {
            display: flex;
        }
    }

    @media (max-width: 767px) {
        .main-header {
            padding: 10px 0;
        }
        .header-logo img {
            max-height: 40px;
        }
    }
</style>

<header class="main-header">
    <div class="header-container">
        <a class="header-logo" href="{{ route('dashboard.index') }}">
            <img src="{{ asset('images/logo' . Request::session()->get('inmobiliaria') . '.png') }}" alt="Logo">
        </a>

        <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="header-nav">
            <a class="header-nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard.index') }}">
                <i class="fas fa-chart-line"></i>
                <strong>Dashboard</strong>
            </a>
            <a class="header-nav-link {{ Request::is('admin/inmuebles') ? 'active' : '' }}" href="/admin/inmuebles">
                <i class="fas fa-house"></i>
                <strong>Inmuebles</strong>
            </a>
            <a class="header-nav-link {{ Request::is('admin/clientes') ? 'active' : '' }}" href="/admin/clientes">
                <i class="fas fa-user"></i>
                <strong>Clientes</strong>
            </a>
            <a class="header-nav-link {{ Request::is('admin/facturacion') ? 'active' : '' }}" href="/admin/facturacion">
                <i class="fas fa-file-invoice"></i>
                <strong>Facturación</strong>
            </a>
            <a class="header-nav-link {{ Request::is('admin/agenda') || Request::is('home') ? 'active' : '' }}" href="/admin/agenda">
                <i class="fas fa-book"></i>
                <strong>Agenda</strong>
            </a>
            <a class="header-nav-link {{ Request::is('admin/inmuebles/idealista*') ? 'active' : '' }}" href="{{ route('inmuebles.idealista') }}">
                <i class="fas fa-building"></i>
                <strong>Idealista</strong>
            </a>
            @if (Request::session()->get('inmobiliaria') == 'sayco')
                <a class="header-nav-link {{ Request::is('seleccion') ? 'active' : '' }}" href="{{ route('cambio', ['boton' => 'sancer']) }}">
                    <i class="fas fa-arrows-rotate"></i>
                    <strong>Ir a SANCER</strong>
                </a>
            @else
                <a class="header-nav-link {{ Request::is('seleccion') ? 'active' : '' }}" href="{{ route('cambio', ['boton' => 'sayco']) }}">
                    <i class="fas fa-arrows-rotate"></i>
                    <strong>Ir a SAYCO</strong>
                </a>
            @endif
        </nav>
    </div>
</header>

<div class="mobile-menu-overlay" id="mobileMenuOverlay">
    <div class="mobile-menu-panel">
        <div class="mobile-menu-header">
            <h5>Menú</h5>
            <button type="button" class="mobile-menu-close" id="mobileMenuClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-menu-body">
            <a class="mobile-menu-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard.index') }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a class="mobile-menu-link {{ Request::is('admin/inmuebles') ? 'active' : '' }}" href="/admin/inmuebles">
                <i class="fas fa-house"></i>
                <span>Inmuebles</span>
            </a>
            <a class="mobile-menu-link {{ Request::is('admin/clientes') ? 'active' : '' }}" href="/admin/clientes">
                <i class="fas fa-user"></i>
                <span>Clientes</span>
            </a>
            <a class="mobile-menu-link {{ Request::is('admin/facturacion') ? 'active' : '' }}" href="/admin/facturacion">
                <i class="fas fa-file-invoice"></i>
                <span>Facturación</span>
            </a>
            <a class="mobile-menu-link {{ Request::is('admin/agenda') || Request::is('home') ? 'active' : '' }}" href="/admin/agenda">
                <i class="fas fa-book"></i>
                <span>Agenda</span>
            </a>
            <a class="mobile-menu-link {{ Request::is('admin/inmuebles/idealista*') ? 'active' : '' }}" href="{{ route('inmuebles.idealista') }}">
                <i class="fas fa-building"></i>
                <span>Idealista</span>
            </a>
            @if (Request::session()->get('inmobiliaria') == 'sayco')
                <a class="mobile-menu-link {{ Request::is('seleccion') ? 'active' : '' }}" href="{{ route('cambio', ['boton' => 'sancer']) }}">
                    <i class="fas fa-arrows-rotate"></i>
                    <span>Ir a SANCER</span>
                </a>
            @else
                <a class="mobile-menu-link {{ Request::is('seleccion') ? 'active' : '' }}" href="{{ route('cambio', ['boton' => 'sayco']) }}">
                    <i class="fas fa-arrows-rotate"></i>
                    <span>Ir a SAYCO</span>
                </a>
            @endif
        </div>
    </div>
</div>

<script>
(function() {
    var menuOpen = false;
    var btn, overlay, closeBtn, links;

    function initMobileMenu() {
        btn = document.getElementById('mobileMenuToggle');
        overlay = document.getElementById('mobileMenuOverlay');
        closeBtn = document.getElementById('mobileMenuClose');
        links = document.querySelectorAll('.mobile-menu-link');

        if (!btn || !overlay) {
            return false;
        }

        function openMenu() {
            if (menuOpen || !overlay) return;
            menuOpen = true;
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            if (!menuOpen || !overlay) return;
            menuOpen = false;
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Limpiar listeners anteriores si existen
        var newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        btn = newBtn;

        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                openMenu();
                return false;
            }, true);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                closeMenu();
                return false;
            }, true);
        }

        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    closeMenu();
                }
            }, true);
        }

        if (links && links.length > 0) {
            links.forEach(function(link) {
                link.addEventListener('click', function() {
                    setTimeout(function() {
                        menuOpen = false;
                        if (overlay) {
                            overlay.classList.remove('active');
                            document.body.style.overflow = '';
                        }
                    }, 200);
                });
            });
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992 && menuOpen) {
                closeMenu();
            }
        });

        return true;
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initMobileMenu();
        });
    } else {
        initMobileMenu();
    }

    // También intentar después de que todo se cargue
    window.addEventListener('load', function() {
        if (!btn || !overlay) {
            initMobileMenu();
        }
    });

    // Intentar después de un delay por si hay scripts que modifican el DOM
    setTimeout(function() {
        if (!btn || !overlay) {
            initMobileMenu();
        }
    }, 100);
})();
</script>

