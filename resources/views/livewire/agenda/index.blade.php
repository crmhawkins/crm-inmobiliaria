<div class="container mx-auto">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <style>
        .page-header-modern {
            background: var(--corporate-green-gradient);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            margin-bottom: 0;
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.2);
        }
        .page-header-modern h5 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header-modern h5 i {
            font-size: 1.8rem;
        }
        .card-modern {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        @media screen and (max-width:1000px) {
            .fc .fc-daygrid-day-frame {
                font-size: 0.8em;
            }
            .fc .fc-button .fc-icon {
                font-size: 0.8em !important;
            }
            .fc .fc-toolbar-title {
                font-size: 1em !important;
            }
        }
        #calendar-container {
            display: flex;
            flex-direction: column;
        }
    </style>
    <div class="card card-modern mb-4">
        <div class="page-header-modern">
            <h5>
                <i class="fas fa-calendar-alt"></i>
                Calendario de Agenda
            </h5>
        </div>
        <div class="card-body p-4" x-data="{}" x-init="$nextTick(() => {
            console.log('hola');
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth < 1000 ? 'timeGridDay' : 'dayGridMonth',
                themeSystem: 'bootstrap',
                locale: 'es',
                // Asegurar que se muestren eventos pasados
                validRange: null, // Permite navegar a fechas pasadas
                showNonCurrentDates: true, // Mostrar fechas de otros meses
                views: {
                    timeGridDay: {
                        dayHeaderFormat: {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        }
                    },
                    dayGridMonth: {
                        monthHeaderFormat: {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        }
                    },
                    timeGridDay: {
                        dayHeaderFormat: {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        }
                    },
                    listWeek: {
                        weekHeaderFormat: {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        }
                    },
                },
                @mobile
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: '',
                },
                footerToolbar: {
                    left: 'dayGridMonth,timeGridDay,listWeek'
                },
                @elsemobile
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
                },
                @endmobile
                eventClick: function(info) {
                    Livewire.emit('seleccionarProducto', info.event.id);
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                    list: 'Lista'
                },
                events: [
                    @foreach($eventos as $evento) {
                        title: '{{ $evento->titulo }}',
                        start: '{{ $evento->fecha_inicio }}',
                        end: '{{ $evento->fecha_fin }}',
                        description: '{{ $evento->descripcion }}',
                        id: '{{ $evento->id }}'
                    },
                    @endforeach
                ],
                eventDidMount: function(info) {
                    var tooltip = new bootstrap.Tooltip(info.el, {
                        title: info.event.title + ': ' + info.event.extendedProps.description,
                        placement: 'top',
                        trigger: 'hover',
                        html: true
                    });
                },
            });

            calendar.render();
        })">
            <div id='calendar'></div>
        </div>
    </div>
</div>
