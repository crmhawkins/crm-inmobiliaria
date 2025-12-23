<div class="container-fluid px-0">
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
        /* Estilos para móvil y tablet */
        @media screen and (max-width: 991px) {
            .container-fluid.px-0 {
                padding: 0 !important;
            }

            .card-modern {
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .page-header-modern {
                padding: 12px 10px !important;
                border-radius: 0 !important;
            }

            .page-header-modern h5 {
                font-size: 1rem !important;
                margin: 0 !important;
            }

            .page-header-modern h5 i {
                font-size: 1.1rem !important;
            }

            .card-body {
                padding: 8px 5px !important;
            }

            /* FullCalendar - Toolbar compacto */
            .fc .fc-toolbar {
                flex-direction: row;
                gap: 5px;
                padding: 5px 3px !important;
                margin-bottom: 5px !important;
            }

            .fc .fc-toolbar-title {
                font-size: 0.85rem !important;
                margin: 0 !important;
                padding: 0 5px;
                flex: 1;
                text-align: center;
            }

            .fc .fc-button-group {
                display: flex;
                gap: 3px;
            }

            .fc .fc-button {
                padding: 6px 10px !important;
                font-size: 0.75rem !important;
                min-height: 36px;
                border-radius: 6px;
            }

            .fc .fc-button .fc-icon {
                font-size: 0.8em !important;
            }

            .fc .fc-prev-button,
            .fc .fc-next-button {
                min-width: 36px;
                min-height: 36px;
                padding: 6px !important;
            }

            /* Footer toolbar compacto */
            .fc .fc-footer-toolbar {
                padding: 5px 3px !important;
                margin-top: 5px !important;
            }

            .fc .fc-footer-toolbar .fc-button-group {
                display: flex;
                gap: 5px;
                width: 100%;
                justify-content: center;
            }

            .fc .fc-footer-toolbar .fc-button {
                flex: 1;
                min-width: 80px;
                font-size: 0.8rem !important;
                padding: 10px 6px !important;
                min-height: 40px;
                white-space: nowrap !important;
                overflow: visible !important;
                text-overflow: clip !important;
                line-height: 1.2;
            }

            .fc .fc-footer-toolbar .fc-button .fc-button-text {
                white-space: nowrap !important;
                display: inline !important;
                word-break: keep-all !important;
            }

            /* Vista de día - MUY COMPACTA */
            .fc .fc-timeGridDay-view .fc-timegrid-slot {
                min-height: 30px !important;
                height: 30px !important;
            }

            .fc .fc-timeGridDay-view .fc-timegrid-slot-label {
                font-size: 0.7rem;
                padding: 2px 4px;
            }

            .fc .fc-timeGridDay-view .fc-timegrid-axis {
                width: 40px !important;
                font-size: 0.7rem;
                padding: 0 3px;
            }

            .fc .fc-timeGridDay-view .fc-timegrid-axis-frame {
                font-size: 0.7rem;
            }

            .fc .fc-timeGridDay-view .fc-timegrid-col {
                font-size: 0.8rem;
            }

            .fc .fc-timeGridDay-view .fc-timegrid-col-frame {
                min-height: auto;
            }

            .fc .fc-timeGridDay-view .fc-timegrid-event {
                font-size: 0.75rem;
                padding: 2px 4px;
                margin: 1px 0;
            }

            /* Vista de mes compacta */
            .fc .fc-daygrid-day-frame {
                font-size: 0.7rem;
                padding: 1px;
                min-height: 60px;
            }

            .fc .fc-daygrid-day-number {
                padding: 2px 4px;
                font-size: 0.8rem;
            }

            .fc .fc-daygrid-event {
                font-size: 0.65rem;
                padding: 1px 3px;
                margin: 0.5px 0;
                line-height: 1.2;
            }

            .fc .fc-daygrid-day {
                min-height: 60px;
            }

            /* Vista de lista compacta */
            .fc .fc-list-event {
                padding: 8px 5px;
            }

            .fc .fc-list-event-title {
                font-size: 0.85rem;
            }

            .fc .fc-list-event-time {
                font-size: 0.8rem;
            }

            /* Reducir espacios generales */
            .fc .fc-scroller {
                -webkit-overflow-scrolling: touch;
                padding: 0;
            }

            .fc .fc-view-harness {
                min-height: 400px;
            }

            /* Ocultar elementos innecesarios */
            .fc .fc-daygrid-day-top {
                flex-direction: column;
            }

            .fc .fc-col-header-cell {
                padding: 5px 2px;
                font-size: 0.75rem;
            }
        }

        @media screen and (max-width: 576px) {
            .page-header-modern {
                padding: 10px 8px !important;
            }

            .page-header-modern h5 {
                font-size: 0.9rem !important;
            }

            .page-header-modern h5 i {
                font-size: 1rem !important;
            }

            .card-body {
                padding: 5px 3px !important;
            }

            .fc .fc-toolbar {
                padding: 3px 2px !important;
            }

            .fc .fc-toolbar-title {
                font-size: 0.8rem !important;
            }

            .fc .fc-button {
                padding: 5px 8px !important;
                font-size: 0.7rem !important;
                min-height: 32px;
            }

            .fc .fc-prev-button,
            .fc .fc-next-button {
                min-width: 32px;
                min-height: 32px;
                padding: 5px !important;
            }

            .fc .fc-footer-toolbar {
                padding: 3px 2px !important;
            }

            .fc .fc-footer-toolbar .fc-button {
                font-size: 0.75rem !important;
                padding: 8px 4px !important;
                min-height: 36px;
                min-width: 70px;
                white-space: nowrap !important;
                overflow: visible !important;
                line-height: 1.2;
            }

            .fc .fc-footer-toolbar .fc-button .fc-button-text {
                white-space: nowrap !important;
                display: inline !important;
                word-break: keep-all !important;
            }

            /* Vista de día aún más compacta */
            .fc .fc-timeGridDay-view .fc-timegrid-slot {
                min-height: 25px !important;
                height: 25px !important;
            }

            .fc .fc-timeGridDay-view .fc-timegrid-axis {
                width: 35px !important;
                font-size: 0.65rem;
            }

            .fc .fc-daygrid-day-frame {
                font-size: 0.65rem;
                min-height: 50px;
            }

            .fc .fc-daygrid-day {
                min-height: 50px;
            }

            .fc .fc-daygrid-event {
                font-size: 0.6rem;
                padding: 1px 2px;
            }
        }
        #calendar-container {
            display: flex;
            flex-direction: column;
        }

        #calendar {
            width: 100%;
        }

        /* Mejoras generales para FullCalendar */
        .fc {
            font-size: 0.9rem;
        }

        /* Reducir espacios en todas las vistas */
        .fc .fc-header-toolbar {
            margin-bottom: 0.5em;
        }

        .fc .fc-view-harness {
            min-height: 300px;
        }

        /* En móvil, hacer el calendario más compacto */
        @media screen and (max-width: 991px) {
            .fc {
                font-size: 0.8rem;
            }

            .fc .fc-view-harness {
                min-height: 350px;
            }

            /* Reducir padding del calendario */
            .fc .fc-scroller-liquid-absolute {
                padding: 0;
            }

            /* Hacer que las celdas sean más compactas */
            .fc .fc-timegrid-body {
                min-height: auto;
            }
        }

        .fc .fc-button-primary {
            background-color: var(--corporate-green);
            border-color: var(--corporate-green);
        }

        .fc .fc-button-primary:hover {
            background-color: var(--corporate-green-dark);
            border-color: var(--corporate-green-dark);
        }

        .fc .fc-button-primary:disabled {
            background-color: var(--corporate-green);
            border-color: var(--corporate-green);
            opacity: 0.6;
        }

        .fc .fc-button-active {
            background-color: var(--corporate-green-dark) !important;
            border-color: var(--corporate-green-dark) !important;
        }
    </style>
    <div class="card card-modern mb-4" style="margin: 0;">
        <div class="page-header-modern">
            <h5>
                <i class="fas fa-calendar-alt"></i>
                Calendario de Agenda
            </h5>
        </div>
        <div class="card-body p-2" style="overflow-x: auto; padding: 5px !important;" x-data="{}" x-init="$nextTick(() => {
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
                        },
                        slotMinTime: '06:00:00',
                        slotMaxTime: '22:00:00',
                        slotDuration: '00:30:00',
                        slotLabelInterval: '01:00:00',
                        allDaySlot: false,
                        height: 'auto'
                    },
                    dayGridMonth: {
                        monthHeaderFormat: {
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
                        },
                        listDayFormat: { weekday: 'short', day: 'numeric', month: 'short' }
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
