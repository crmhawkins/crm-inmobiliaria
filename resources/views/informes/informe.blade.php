<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }

        .box {
            display: flex;
            justify-content: space-between;
        }
        th.iva{
            text-align: end !important;
        }
    </style>

</head>

<body>

    @switch($tipo_informe)
        @case(1)
            <h1>{{ $nombreInforme }}</h1>
            <p style="margin-bottom:5px;"><b>Periodo:</b> {{ date_format(date_create($fecha_inicio), 'd/m/Y') }} -
                {{ date_format(date_create($fecha_fin), 'd/m/Y') }} </p>

            <p><b>Servicio:</b> {{ $servicio }} </p>
            <br>
            <table width="100%">
                <thead style="background-color: lightgray;">
                    <th>Grupo ID</th>
                    <th>Ventas</th>
                    <th>Importe</th>
                </thead>
                <tbody>
                    @foreach ($datos as $dato)
                        <tr>
                            <td>{{ $dato['grupo_id'] }}</td>
                            <td>{{ $dato['ventas'] }}</td>
                            <td>{{ $dato['importe'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @break

        @case(2)
            <h1>{{ $nombreInforme }}</h1>
            <p style="margin-bottom:5px;"><b>Periodo:</b> {{ date_format(date_create($fecha_inicio), 'd/m/Y') }} -
                {{ date_format(date_create($fecha_fin), 'd/m/Y') }} </p>

            <p><b>Servicio:</b> {{ $servicio }} </p>
            <br>
            @foreach ($datos as $dato)
                <table width="100%">
                    <thead>
                        <tr style="background-color: lightgray;">
                            <th width="30%">Grupo ID</th>
                            <th width="50%"></th>
                            <th width="10%"></th>
                            <th width="10%"></th>
                        </tr>
                        <tr style="text-align: center">
                            <th>{{ $dato['grupo_id'] }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr style="background-color: lightgray;">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Unidades</th>
                            <th>Importe</th>

                    </thead>
                    <tbody>
                        @foreach ($dato['productos'] as $datProducto)
                            <tr style="text-align: center">
                                <td>{{ $datProducto['cod_producto'] }}</td>
                                <td>{{ $datProducto['descripcion'] }}</td>
                                <td>{{ $datProducto['cantidad'] }}</td>
                                <td>{{ $datProducto['precio_venta'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="text-align: center">
                            <th></th>
                            <th style="text-align:end !important;"> TOTAL:</th>
                            <th>{{ $dato['ventas'] }}</th>
                            <th>{{ $dato['importe'] }}</th>
                        </tr>
                    </tfoot>
                </table>
                <br>
            @endforeach
        @break

        @case(5)
            <h1>{{ $nombreInforme }}</h1>
            <p style="margin-bottom:5px;"><b>Periodo:</b> {{ date_format(date_create($fecha_inicio), 'd/m/Y') }} -
                {{ date_format(date_create($fecha_fin), 'd/m/Y') }} </p>

            <p><b>Servicio:</b> {{ $servicio }} </p>
            <br>
            <div class="box">
                <div><b>Cliente:</b> {{ $cliente }} - {{ $clienteName }} </div>
                <div><b>Total:</b> {{ $total }} </div>
            </div>
            <br>
            @foreach ($datos as $dato)
                <table width="100%">
                    <thead>
                        <tr style="background-color: lightgray;">
                            <th width="15%">Fecha</th>
                            <th width="35%"></th>
                            <th width="5%">Albarán</th>
                            <th width="10%"></th>
                            <th width="10%">Matricula</th>
                            <th width="10%"></th>
                            <th width="15%">Servicio</th>
                        </tr>
                        <tr style="text-align: center">
                            <th>{{ date_format(date_create($dato['fecha'])  , 'd/m/Y') }}</th>
                            <th></th>
                            <th>{{ $dato['albaran'] }}</th>
                            <th></th>
                            <th>{{ $dato['matricula'] }}</th>
                            <th></th>
                            <th>{{ $dato['servicio'] }}</th>
                        </tr>
                        <tr style="background-color: lightgray;">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Cant.</th>
                            <th>P.V.P.</th>
                            <th>Descuento %</th>
                            <th>Ecotasa</th>
                            <th>Importe</th>

                    </thead>
                    <tbody>
                        @foreach ($dato['productos'] as $datProducto)
                            <tr style="text-align: center">
                                <td>{{ $datProducto['cod_producto'] }}</td>
                                <td>{{ $datProducto['descripcion'] }}</td>
                                <td>{{ $datProducto['cantidad'] }}</td>
                                <td>{{ $datProducto['precio_venta'] }} €</td>
                                <td>{{ $datProducto['descuento'] / $datProducto['precio_venta'] }}%</td>
                                <td>{{ $datProducto['ecotasa'] }} €</td>
                                <td>{{ $datProducto['precio_venta'] * $datProducto['cantidad'] }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="border-top: 1px solid #000 !important;">
                        <tr style="text-align: center;">
                            <th colspan="2"></th>
                            <th colspan="2" style="align-self: start !important;"> IVA: {{ $dato['iva'] }} €</th>
                            <th colspan="2" style="text-align:end !important;"> TOTAL ALBARÁN:</th>
                            <th>{{ $dato['total'] }} €</th>
                        </tr>
                    </tfoot>
                </table>
                <br>
            @endforeach
        @break

        @default
            <p>"Hola"</p>
    @endswitch

</body>

</html>
