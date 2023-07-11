<div>
    @mobile
        @if ($movimiento != null)
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab1')">Consultar
                                movimientos</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block" wire:click="cambioTab('tab2')">Ver
                                movimiento</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Hacer
                                movimiento</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('caja.index-component')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Consultar movimientos</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab2')">Ver
                                movimiento</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Hacer
                                movimiento</button>
                        </div>

                    </div>
                    <br>
                </div>
                <br>

                @livewire('caja.edit-component', ['identificador' => $movimiento], key('tab2'))

                <br>
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Consultar movimientos</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver
                                movimiento</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab3')">Hacer
                                movimiento</button>
                        </div>

                    </div>
                    <br>
                </div>
                <br>

                @if ($factura != null)
                    @livewire('caja.create-component', ['factura' => $factura], key('tab3'))
                @else
                    @livewire('caja.create-component', key('tab3'))
                @endif
            @endif
        @else
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div>
                        <div class="row">
                            <div class="col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab1')">Consultar movimientos</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                    wire:click="cambioTab('tab2')">Ver/Editar
                                    movimiento</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="me-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab3')">Añadir
                                    movimiento</button>
                            </div>

                        </div>
                        <br>
                    </div>
                </div>
                <br>

                @livewire('caja.index-component')
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Consultar movimientos</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block"
                                wire:click="cambioTab('tab2')" disabled>Ver
                                movimiento</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab3')">Hacer
                                movimiento</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @if ($factura != null)
                    @livewire('caja.create-component', ['factura' => $factura], key('tab3'))
                @else
                    @livewire('caja.create-component', key('tab3'))
                @endif

                <br>
            @endif
        @endif
    @elsemobile
        @if ($movimiento != null)
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3>Consultar movimientos</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Ver
                                movimiento</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Hacer
                                movimiento</h5>
                        </button>
                    </li>
                </ul>
                <br>

                @livewire('caja.index-component')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Consultar movimientos</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')">
                            <h3>Ver
                                movimiento</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Hacer
                                movimiento</h5>
                        </button>
                    </li>
                </ul>
                <br>

                @livewire('caja.edit-component', ['identificador' => $movimiento], key('tab2'))

                <br>
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Consultar movimientos</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Ver
                                movimiento</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')">
                            <h3>Hacer
                                movimiento</h3>
                        </button>
                    </li>
                </ul>
                <br>

                @if ($factura != null)
                    @livewire('caja.create-component', ['factura' => $factura], key('tab3'))
                @else
                    @livewire('caja.create-component', key('tab3'))
                @endif
            @endif
        @else
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3>Consultar movimientos</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')" disabled>
                            <h5>Ver
                                movimiento</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Hacer
                                movimiento</h5>
                        </button>
                    </li>
                </ul>
                <br>

                @livewire('caja.index-component')
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Consultar movimientos</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')" disabled>
                            <h5>Ver
                                movimiento</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')">
                            <h3>Hacer
                                movimiento</h3>
                        </button>
                    </li>
                </ul>
                <br>

                @if ($factura != null)
                    @livewire('caja.create-component', ['factura' => $factura], key('tab3'))
                @else
                    @livewire('caja.create-component', key('tab3'))
                @endif
                <br>
            @endif
        @endif
    @endmobile

</div>
