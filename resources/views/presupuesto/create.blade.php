@extends('layouts.app')

@section('content')

@section('title', 'Presupuestos')


<div>
    @livewire('presupuestos.create-component')
</div>

@endsection

@section('scripts')
    {{-- <script>
        $(document).ready(function() {
            updateDisplay();
            $('#tableBusqueda').DataTable({
                responsive: true,
                ordering: false,
                select: true,
                dom: 'lfrtip',
                'language': {
                    select: {
                        rows: {
                            _: '%d artículos seleccionados.',
                            1: '1 artículo seleccionado.'
                        }
                    },
                    'lengthMenu': 'Mostrando _MENU_ registros por página',
                    'zeroRecords': 'Nothing found - sorry',
                    'info': '',
                    'infoEmpty': 'No hay registros disponibles',
                    'infoFiltered': '(filtrado de _MAX_ total registros)',
                    'search': 'Buscar artículo:',
                    'paginate': {
                        'first': 'Primero',
                        'last': 'Ultimo',
                        'next': 'Siguiente',
                        'previous': 'Anterior'
                    },
                    'zeroRecords': 'No se encontraron registros coincidentes',
                }
            });

        });

        $(document).on("click", "#botonProducto", function() {
            var data = document.getElementsByClassName("selected")[0].id;
            console.log(data);
            Livewire.emit('añadirProducto', data, count);
            count = 1;
            window.Livewire.restart();
        });


        let counterDisplayElem = document.querySelector('.counter-display');
        let counterMinusElem = document.querySelector('.counter-minus');
        let counterPlusElem = document.querySelector('.counter-plus');

        let count = 1;

        updateDisplay();

        counterPlusElem.addEventListener("click", () => {
            count++;
            updateDisplay();
        });

        counterMinusElem.addEventListener("click", () => {
            count--;
            updateDisplay();
        });

        function updateDisplay() {
            counterDisplayElem.innerHTML = count;
        };
    </script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.2/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.2/js/select.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.2/js/select.dataTables.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.2/js/select.html5.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection --}}
