<div>
    @mobile
        @if ($ecotasa != null)
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab1')">Ver ecotasas (diámetro < 1400mm)</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver ecotasas (diámetro > 1400mm)</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Modificar datos de ecotasa</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Añadir datos de ecotasa</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('ecotasa.index')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Ver ecotasas (diámetro < 1400mm)</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab2')">Ver ecotasas (diámetro > 1400mm)</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Modificar datos de ecotasa</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Añadir datos de ecotasa</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>


                @livewire('ecotasa.index2')

                <br>
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Ver ecotasas (diámetro < 1400mm)</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver ecotasas (diámetro > 1400mm)</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab3')">Modificar datos de ecotasa</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Añadir datos de ecotasa</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('ecotasa.edit', ['identificador' => $ecotasa], key('tab3'))
            @elseif ($tab == 'tab4')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Ver ecotasas (diámetro < 1400mm)</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver ecotasas (diámetro > 1400mm)</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Modificar datos de ecotasa</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab4')">Añadir datos de ecotasa</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>
                @livewire('ecotasa.create')
            @endif
        @else
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div>
                        <div class="row">
                            <div class="col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab1')">Ver ecotasas (diámetro < 1400mm)</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab2')">Ver ecotasas (diámetro > 1400mm)</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                    wire:click="cambioTab('tab3')">Asignar/Editar
                                    ecotasa</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab4')">Añadir datos de ecotasa</button>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <br>

                @livewire('ecotasa.index')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Ver ecotasas (diámetro < 1400mm)</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver ecotasas (diámetro > 1400mm)</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                wire:click="cambioTab('tab3')">Modificar datos de ecotasa</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Añadir datos de ecotasa</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('ecotasa.index2')

                <br>
            @elseif ($tab == 'tab4')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Ver ecotasas (diámetro < 1400mm)</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Ver ecotasas (diámetro > 1400mm)</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                wire:click="cambioTab('tab3')">Modificar datos de ecotasa</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab4')">Añadir datos de ecotasa</button>
                        </div>
                    </div>
                    <br>
                </div>

                <br>

                @livewire('ecotasa.create')

            @endif
        @endif
    @elsemobile
        @if ($ecotasa != null)
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')"><h3>Ver ecotasas (diámetro < 1400mm)</h3></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Ver ecotasas (diámetro > 1400mm) </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"> <h5> Asignar/Editar
                            ecotasa</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"> <h5> Añadir datos de ecotasa</h5></button>
                    </li>
                </ul>

                @livewire('ecotasa.index')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"> <h5> Ver ecotasas (diámetro < 1400mm)</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')"> <h3>Ver ecotasas (diámetro > 1400mm)</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"> <h5> Asignar/Editar
                            ecotasa</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"> <h5> Añadir datos de ecotasa</h5></button>
                    </li>
                </ul>
                <br>


                @livewire('ecotasa.index2')

                <br>
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"> <h5> Ver ecotasas (diámetro < 1400mm)</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Ver ecotasas (diámetro > 1400mm) </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')"><h3>Asignar/Editar
                            ecotasa</h3></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"> <h5> Añadir datos de ecotasa </h5></button>
                    </li>
                </ul>
                <br>

                @livewire('ecotasa.edit', ['identificador' => $ecotasa], key('tab3'))
            @elseif ($tab == 'tab4')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"> <h5> Ver ecotasas (diámetro < 1400mm)</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Ver ecotasas (diámetro > 1400mm) </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"> <h5> Asignar/Editar
                            ecotasa</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab4')"><h3>Añadir datos de ecotasa</h3></button>
                    </li>
                </ul>
                <br>

                @livewire('ecotasa.create')

            @endif
        @else
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3> Ver ecotasas (diámetro < 1400mm) </h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Ver ecotasas (diámetro > 1400mm) </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled>
                            <h5> Modificar datos de ecotasa </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')">
                            <h5> Añadir datos de ecotasa </h5>
                        </button>
                    </li>
                </ul>
                <br>
                @livewire('ecotasa.index')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"><h5> Ver ecotasas (diámetro < 1400mm)  </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')"> <h3> Ver ecotasas (diámetro > 1400mm) </h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled><h5> Asignar/Editar
                            ecotasa </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"><h5> Añadir datos de ecotasa </h5> </button>
                    </li>
                </ul>
                <br>

                @livewire('ecotasa.index2')

                <br>
            @elseif ($tab == 'tab4')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"><h5> Ver ecotasas (diámetro < 1400mm) </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Ver ecotasas (diámetro > 1400mm)
                        </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled> <h5> Asignar/Editar
                            ecotasa </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab4')"><h3> Añadir datos de ecotasa </h3> </button>
                    </li>
                </ul>
                <br>

                @livewire('ecotasa.create')

            @endif
        @endif
    @endmobile

</div>
