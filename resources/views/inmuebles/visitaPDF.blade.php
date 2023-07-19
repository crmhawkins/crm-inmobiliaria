<!DOCTYPE html>
<html>

<head>
    @php
        use App\Models\Caracteristicas;
    @endphp
    <title>Hoja de Visita</title>
    <style type="text/css">
        body {
            font-family: 'Roboto Condensed', sans-serif;
        }

        .m-0 {
            margin: 0px;
        }

        .p-0 {
            padding: 0px;
        }

        .pt-5 {
            padding-top: 5px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .text-center {
            text-align: center !important;
        }

        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50%;
        }

        .w-85 {
            width: 85%;
        }

        .w-15 {
            width: 15%;
        }

        .logo img {
            width: 200px;
            height: 60px;
        }

        .gray-color {
            color: #5D5D5D;
        }

        .text-bold {
            font-weight: bold;
        }

        .border {
            border: 1px solid black;
        }

        table tr,
        th,
        td {
            border: 1px solid #d2d2d2;
            border-collapse: collapse;
            padding: 7px 8px;
        }

        table tr th {
            background: #F4F4F4;
            font-size: 15px;
        }

        table tr td {
            font-size: 13px;
        }

        table {
            border-collapse: collapse;
        }

        .box-text p {
            line-height: 10px;
        }

        .float-left {
            float: left;
        }

        .total-part {
            font-size: 16px;
            line-height: 12px;
        }

        .total-right p {
            padding-right: 20px;
        }
    </style>
</head>

<body>
    <h1>Hoja de Visita</h1>

    <h2>Información del Cliente</h2>
    <table>
        <tr>
            <th>Nombre Completo</th>
            <td>{{ $datos['cliente']['nombre_completo'] }}</td>
        </tr>
        <tr>
            <th>DNI</th>
            <td>{{ $datos['cliente']['dni'] }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $datos['cliente']['email'] }}</td>
        </tr>
    </table>

    <h2>Información del Inmueble</h2>
    <table>
        <tr>
            <th>Título</th>
            <td>{{ $datos['inmueble']['titulo'] }}</td>

            <th>Descripción</th>
            <td>{{ $datos['inmueble']['descripcion'] }}</td>
        </tr>
        <tr>
            <th>Ubicación</th>
            <td>{{ $datos['inmueble']['ubicacion'] }}</td>

            <th>Tipo de operación</th>
            <td>{{ $datos['inmueble']['disponibilidad'] }}</td>
        </tr>
        <tr>
            <th>Metros cuadrados</th>
            <td>{{ $datos['inmueble']['m2'] }}</td>

            <th>Habitaciones</th>
            <td>{{ $datos['inmueble']['habitaciones'] }}</td>
        </tr>
        <tr>
            <th>Baños</th>
            <td>{{ $datos['inmueble']['banos'] }}</td>

            <th>Tipo de inmueble</th>
            <td>{{ $datos['inmueble']['tipo_vivienda_id'] }}</td>
        </tr>
        <tr>
            <th>Estado del inmueble</th>
            <td>{{ $datos['inmueble']['estado'] }}</td>
            <th>Certificado energético</th>
            <td>{{ $datos['inmueble']['cert_energetico_elegido'] }}</td>
        </tr>
        <tr>
            <th colspan="2">Otras características</th>
            <td colspan="2">
                <ul>
                    @foreach (json_decode($datos['inmueble']['otras_caracteristicas'], true) as $caracteristica)
                        <li> {{ Caracteristicas::where('id', $caracteristica)->first()->nombre }} </li>
                    @endforeach
                </ul>
            </td>
        </tr>
        <!-- Añade más filas de tabla según sea necesario para mostrar la información del inmueble -->
    </table>

    <h2>Firma</h2>
    <img src="{{ public_path('storage/' . $datos['firma']) }}" alt="Firma del Cliente">
</body>

</html>
