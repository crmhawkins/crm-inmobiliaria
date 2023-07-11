<div>
    @mobile
        @if ($datos != null)
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab1')">Filtrar por
                                grupo</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Tipo de informe</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Ver informe</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('informes.grupos')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Filtrar por grupo</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab2')">Tipo de
                                informe</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Ver informe</button>
                        </div>

                    </div>
                    <br>
                </div>
                <br>

                @livewire('informes.index')

                <br>
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Filtrar por grupo</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Tipo de informe</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab3')">Ver
                                informe</button>
                        </div>

                    </div>
                    <br>
                </div>
                @livewire('informes.resultado', ['datos' => $datos, 'tipo_informe' => $tipo_informe, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'servicio' => $servicio])
            @endif
        @else
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div>
                        <div class="row">
                            <div class="col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab1')">Filtrar por grupo</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab2')">Tipo de informe</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="me-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                    wire:click="cambioTab('tab3')">Ver informe</button>
                            </div>

                        </div>
                        <br>
                    </div>
                </div>
                <br>

                @livewire('informes.grupos')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Filtrar por grupo</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab2')">Tipo de
                                informe</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="me-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block"
                                wire:click="cambioTab('tab3')" disabled>Ver informe</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>
                @livewire('informes.index')

                <br>
            @endif
        @endif
    @elsemobile
        @if ($datos != null)
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3>Filtrar por grupo</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Tipo de informe</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Ver informe</h5>
                        </button>
                    </li>
                </ul>
                <br>

                @livewire('informes.grupos')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Filtrar por grupo</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')">
                            <h3>Tipo de informe</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')">
                            <h5>Ver informe</h5>
                        </button>
                    </li>
                </ul>
                <br>
                @livewire('informes.index')


                <br>
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Filtrar por grupo</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Tipo de informe</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')">
                            <h3>Ver informe</h3>
                        </button>
                    </li>
                </ul>

                @livewire('informes.resultado', ['datos' => $datos, 'tipo_informe' => $tipo_informe, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'servicio' => $servicio])
                <br>
            @endif
        @else
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3>Filtrar por grupo</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Tipo de informe</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled>
                            <h5>Ver informe</h5>
                        </button>
                    </li>
                </ul>
                <br>
                @livewire('informes.grupos')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')">
                            <h5>Filtrar por grupo</h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')">
                            <h3>Tipo de informe</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled>
                            <h5>Ver informe</h5>
                        </button>
                    </li>
                </ul>
                <br>

                @livewire('informes.index')
            @endif
        @endif
    @endmobile

</div>
