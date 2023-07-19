<div>
    @mobile
        @if ($cliente != null)
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab1')">Alta</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab2')">Editar</button>
                            </div>
                        </div>
                        <br>
                    @endif
                    <div class="row">
                        <div class="ms-auto col-12 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Buscar</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('clientes.create')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab1')">Alta</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab2')">Editar</button>
                            </div>
                        </div>
                        <br>
                    @endif
                    <div class="row">
                        <div class="ms-auto col-12 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Buscar</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('clientes.edit', ['identificador' => $cliente], key('tab2'))
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab1')">Alta</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab2')">Editar</button>
                            </div>
                        </div>
                        <br>
                    @endif
                    <div class="row">
                        <div class="ms-auto col-12 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab3')">Buscar</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('clientes.index')
            @endif
        @else
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab1')">Alta</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-block"
                                    wire:click="cambioTab('tab2')">Editar</button>
                            </div>
                        </div>
                        <br>
                    @endif
                    <div class="row">
                        <div class="ms-auto col-12 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Buscar</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('clientes.create')
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab1')">Alta</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-block"
                                    wire:click="cambioTab('tab2')">Editar</button>
                            </div>
                        </div>
                        <br>
                    @endif
                    <div class="row">
                        <div class="ms-auto col-12 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab3')">Buscar</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('clientes.index')
            @endif
        @endif
    @elsemobile
        @if ($cliente != null)
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <li class="nav-item">
                            <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                                <h3>Alta</h3>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                                <h5>Editar</h5>
                            </button>
                        </li>
                    @endif
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Buscar</h5>
                        </button>
                    </li>

                </ul>

                @livewire('clientes.create')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <li class="nav-item">
                            <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                                <h5>Alta</h5>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link active" wire:click.prevent="cambioTab('tab2')">
                                <h3>Editar</h3>
                            </button>
                        </li>
                    @endif
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Buscar</h5>
                        </button>
                    </li>
                </ul>
                <br>

                @livewire('clientes.edit', ['identificador' => $cliente], key('tab3'))

                <br>
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <li class="nav-item">
                            <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                                <h5>Alta</h5>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                                <h5>Editar</h5>
                            </button>
                        </li>
                    @endif
                    <li class="nav-item active">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')">
                            <h3>Buscar</h3>
                        </button>
                    </li>

                </ul>
                <br>

                @livewire('clientes.index')
            @endif
        @else
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <li class="nav-item">
                            <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                                <h3>Alta</h3>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" wire:click.prevent="cambioTab('tab2')" disabled>
                                <h5>Editar</h5>
                            </button>
                        </li>
                    @endif
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Buscar</h5>
                        </button>
                    </li>

                </ul>
                <br>

                @livewire('clientes.create')
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    @if (
                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                        <li class="nav-item">
                            <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                                <h5>Alta</h5>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" wire:click.prevent="cambioTab('tab2')" disabled>
                                <h5>Editar</h5>
                            </button>
                        </li>
                    @endif
                    <li class="nav-item active">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')">
                            <h3>Buscar</h3>
                        </button>
                    </li>

                </ul>
                <br>
                @livewire('clientes.index')
            @endif
        @endif
    @endmobile
</div>
