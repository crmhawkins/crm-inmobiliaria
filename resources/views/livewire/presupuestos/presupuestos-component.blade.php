<div>
    @mobile
        @if ($presupuesto != null)
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab1')">Buscador</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver/Editar
                                presupuesto</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Añadir
                                presupuesto</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('presupuestos.index-component')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Buscador</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver/Editar
                                presupuesto</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Añadir
                                presupuesto</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>


                @livewire('presupuestos.edit-component', ['identificador' => $presupuesto], key('tab2'))

                <br>
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Buscador</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver/Editar
                                presupuesto</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab3')">Añadir
                                presupuesto</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('presupuestos.create-component', key('tab3'))
            @elseif ($tab == 'tab4')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Buscador</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver/Editar
                                presupuesto</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Añadir
                                presupuesto</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                <div class="ms-auto col d-grid gap-2">
                    <a class="btn btn-primary btn-block" href="{{ route('clients.index') }}"> Consultar y editar
                        clientes </a>
                    <a class="btn btn-primary btn-block" href="{{ route('productos.index') }}"> Consultar y editar
                        productos </a>
                </div>
            @endif
        @else
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div>
                        <div class="row">
                            <div class="col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab1')">Buscador</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                    wire:click="cambioTab('tab2')">Ver/Editar
                                    presupuesto</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab3')">Añadir
                                    presupuesto</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab4')">Opciones</button>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <br>

                @livewire('presupuestos.index-component', key('tab1'))
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Buscador</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block"
                                wire:click="cambioTab('tab2')" disabled>Ver/Editar
                                presupuesto</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab3')">Añadir
                                presupuesto</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('presupuestos.create-component', key('tab3'))

                <br>
            @elseif ($tab == 'tab4')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Buscador</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block"
                                wire:click="cambioTab('tab2')" disabled>Ver/Editar
                                presupuesto</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Añadir
                                presupuesto</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>

                <br>

                <div class="ms-auto col d-grid gap-2">
                    <a class="btn btn-primary btn-block" href="{{ route('clients.index') }}"> Consultar y
                        editar clientes </a>
                    <a class="btn btn-primary btn-block" href="{{ route('productos.index') }}"> Consultar y editar
                        productos </a>
                </div>
            @endif
        @endif
    @elsemobile
        @if ($presupuesto != null)
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3>Buscador</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Ver/Editar
                                presupuesto</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"><h5>Añadir
                            presupuesto</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"><h5>Opciones</h5></button>
                    </li>
                </ul>
                <br>

                @livewire('presupuestos.index-component')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Buscador</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')"><h3>Ver/Editar
                            presupuesto</h3></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"><h5>Añadir
                            presupuesto</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"><h5>Opciones</h5></button>
                    </li>
                </ul>
                <br>


                @livewire('presupuestos.edit-component', ['identificador' => $presupuesto], key('tab2'))

                <br>
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Buscador</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Ver/Editar
                                presupuesto</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')"><h3>Añadir
                            presupuesto</h3></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"><h5>Opciones</h5></button>
                    </li>
                </ul>
                <br>

                @livewire('presupuestos.create-component', key('tab3'))
            @elseif ($tab == 'tab4')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Buscador</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Ver/Editar
                                presupuesto</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"><h5>Añadir
                            presupuesto</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab4')"><h3>Opciones</h3></button>
                    </li>
                </ul>
                <br>

                <div class="ms-auto col d-grid gap-2">
                    <a class="btn btn-primary btn-block" href="{{ route('clients.index') }}"> Consultar y editar
                        clientes </a>
                    <a class="btn btn-primary btn-block" href="{{ route('productos.index') }}"> Consultar y editar
                        productos </a>
                </div>
            @endif
        @else
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3>Buscador</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')" disabled>
                            <h5>Ver/Editar
                                presupuesto</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"><h5>Añadir
                            presupuesto</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"><h5>Opciones</h5></button>
                    </li>
                </ul>
                <br>

                @livewire('presupuestos.index-component', key('tab1'))
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Buscador</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')" disabled>
                            <h5>Ver/Editar
                                presupuesto</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')"><h3>Añadir
                            presupuesto</h3></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"><h5>Opciones</h5></button>
                    </li>
                </ul>
                <br>

                @livewire('presupuestos.create-component', key('tab3'))

                <br>
            @elseif ($tab == 'tab4')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Buscador</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')" disabled>
                            <h5>Ver/Editar
                                presupuesto</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"><h5>Añadir
                            presupuesto</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab4')"><h3>Opciones</h3></button>
                    </li>
                </ul>
                <br>

                <div class="ms-auto col d-grid gap-2">
                    <a class="btn btn-primary btn-lg" href="{{ route('clients.index') }}"> Consultar y
                        editar clientes </a>
                    <a class="btn btn-primary btn-lg" href="{{ route('productos.index') }}"> Consultar y editar
                        productos </a>
                </div>
            @endif
        @endif
    @endmobile
</div>
