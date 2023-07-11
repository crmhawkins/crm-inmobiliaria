<div>
    @mobile
        @if ($tarea != null)
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab1')">Tareas sin
                                asignar</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Tareas asignadas</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Asignar/Editar
                                tarea</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('orden-trabajo.index-component')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Tareas sin asignar</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block" wire:click="cambioTab('tab2')">Tareas
                                asignadas</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Asignar/Editar
                                tarea</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>


                @livewire('orden-trabajo.index2-component')

                <br>
            @elseif ($tab == 'tab3')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Tareas sin asignar</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Tareas asignadas</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab3')">Asignar/Editar
                                tarea</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('orden-trabajo.edit-component', ['identificador' => $tarea], key('tab3'))
            @elseif ($tab == 'tab4')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Tareas sin asignar</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Tareas asignadas</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab3')">Asignar/Editar
                                tarea</button>
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
                    <a class="btn btn-primary btn-block" href="{{ route('proveedores.index') }}"> Consultar y editar
                        proveedores </a>
                    <a class="btn btn-primary btn-block" href="{{ route('ecotasa.index') }}"> Consultar y editar
                        ecotasas </a>
                </div>
            @endif
        @else
            @if ($tab == 'tab1')
                <div style="border-bottom: 1px solid black !important;">
                    <div>
                        <div class="row">
                            <div class="col-6 d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-block"
                                    wire:click="cambioTab('tab1')">Tareas sin asignar</button>
                            </div>
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-block"
                                    wire:click="cambioTab('tab2')">Tareas asignadas</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="ms-auto col-6 d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                    wire:click="cambioTab('tab3')">Asignar/Editar
                                    tarea</button>
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

                @livewire('orden-trabajo.index-component')
            @elseif ($tab == 'tab2')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Tareas sin asignar</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-block"
                                wire:click="cambioTab('tab2')">Tareas asignadas</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                wire:click="cambioTab('tab3')">Asignar/Editar
                                tarea</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab4')">Opciones</button>
                        </div>
                    </div>
                    <br>
                </div>
                <br>

                @livewire('orden-trabajo.index2-component')

                <br>
            @elseif ($tab == 'tab4')
                <div style="border-bottom: 1px solid black !important;">
                    <div class="row">
                        <div class="col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab1')">Tareas sin asignar</button>
                        </div>
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-block"
                                wire:click="cambioTab('tab2')">Tareas asignadas</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="ms-auto col-6 d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-block" disabled
                                wire:click="cambioTab('tab3')">Asignar/Editar
                                tarea</button>
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
                    <a class="btn btn-primary btn-block" href="{{ route('trabajadores.index') }}"> Consultar y
                        editar proveedores </a>
                </div>
            @endif
        @endif
    @elsemobile
        @if ($tarea != null)
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')"><h3>Tareas sin asignar</h3></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Tareas asignadas </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"> <h5> Asignar/Editar
                            tarea</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"> <h5> Opciones</h5></button>
                    </li>
                </ul>

                @livewire('orden-trabajo.index-component')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"> <h5> Tareas sin asignar</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')"> <h3>Tareas asignadas</h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"> <h5> Asignar/Editar
                            tarea</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"> <h5> Opciones</h5></button>
                    </li>
                </ul>
                <br>


                @livewire('orden-trabajo.index2-component')

                <br>
            @elseif ($tab == 'tab3')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"> <h5> Tareas sin asignar</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Tareas asignadas </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab3')"><h3>Asignar/Editar
                            tarea</h3></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"> <h5> Opciones </h5></button>
                    </li>
                </ul>
                <br>

                @livewire('orden-trabajo.edit-component', ['identificador' => $tarea], key('tab3'))
            @elseif ($tab == 'tab4')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"> <h5> Tareas sin asignar</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Tareas asignadas </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')"> <h5> Asignar/Editar
                            tarea</h5></button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab4')"><h3>Opciones</h3></button>
                    </li>
                </ul>
                <br>

                <div class="ms-auto col d-grid gap-2">
                    <a class="btn btn-primary btn-lg" href="{{ route('proveedores.index') }}"> Consultar y editar
                        proveedores </a>
                    <a class="btn btn-primary btn-lg" href="{{ route('ecotasa.index') }}"> Consultar y editar
                        ecotasas </a>
                </div>
            @endif
        @else
            @if ($tab == 'tab1')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab1')">
                            <h3> Tareas sin asignar </h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')">
                            <h5>Tareas asignadas </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled>
                            <h5> Asignar/Editar
                                tarea </h5>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')">
                            <h5> Opciones </h5>
                        </button>
                    </li>
                </ul>
                <br>
                @livewire('orden-trabajo.index-component')
            @elseif ($tab == 'tab2')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"><h5> Tareas sin asignar  </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab2')"> <h3> Tareas asignadas </h3>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled><h5> Asignar/Editar
                            tarea </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab4')"><h5> Opciones </h5> </button>
                    </li>
                </ul>
                <br>

                @livewire('orden-trabajo.index2-component')

                <br>
            @elseif ($tab == 'tab4')
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab1')"><h5> Tareas sin asignar </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab2')"> <h5> Tareas asignadas
                        </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" wire:click.prevent="cambioTab('tab3')" disabled> <h5> Asignar/Editar
                            tarea </h5> </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link active" wire:click.prevent="cambioTab('tab4')"><h3> Opciones </h3> </button>
                    </li>
                </ul>
                <br>

                <div class="ms-auto col d-grid gap-2">
                    <a class="btn btn-primary btn-lg" href="{{ route('trabajadores.index') }}"> Consultar y
                        editar trabajadores </a>
                </div>
            @endif
        @endif
    @endmobile

</div>
