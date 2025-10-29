<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
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

    .mt-20 {
        margin-top: 20px;
    }

    .mb-10 {
        margin-bottom: 10px;
    }

    .text-center {
        text-align: center !important;
    }

    .text-right {
        text-align: right !important;
    }

    .w-100 {
        width: 100%;
    }

    .w-50 {
        width: 50%;
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
        width: 100%;
    }

    .box-text p {
        line-height: 12px;
        margin: 5px 0;
    }

    .float-left {
        float: left;
    }

    .firma-container {
        border: 2px solid #333;
        padding: 10px;
        margin: 20px 0;
        min-height: 150px;
        text-align: center;
    }

    .firma-imagen {
        max-width: 300px;
        max-height: 150px;
        margin: 10px auto;
        display: block;
    }

    .firma-nombre {
        margin-top: 10px;
        font-weight: bold;
        border-top: 1px solid #333;
        padding-top: 10px;
    }

    .clear {
        clear: both;
    }
</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">HOJA DE FIRMA</h1>
    </div>

    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Fecha de la cita - <span class="gray-color">{{ $fecha }}</span></p>
            @if($evento)
            <p class="m-0 pt-5 text-bold w-100">Título - <span class="gray-color">{{ $evento->titulo }}</span></p>
            @endif
        </div>
        <div class="w-50 float-left text-right mt-10">
            <p class="m-0 pt-5 text-bold">Fecha de firma - <span class="gray-color">{{ $hoja_firma->fecha_firma ? $hoja_firma->fecha_firma->format('d/m/Y H:i') : date('d/m/Y H:i') }}</span></p>
        </div>
        <div class="clear"></div>
    </div>

    @if($cliente || $inmueble)
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                @if($cliente && $inmueble)
                <th class="w-50">Cliente</th>
                <th class="w-50">Inmueble</th>
                @elseif($cliente)
                <th class="w-100">Cliente</th>
                @elseif($inmueble)
                <th class="w-100">Inmueble</th>
                @endif
            </tr>
            <tr>
                @if($cliente)
                <td>
                    <div class="box-text">
                        <p><strong>{{ $cliente->nombre_completo }}</strong></p>
                        @if($cliente->dni)
                        <p>DNI: {{ $cliente->dni }}</p>
                        @endif
                        @if($cliente->direccion)
                        <p>Dirección: {{ $cliente->direccion }}</p>
                        @endif
                        @if($cliente->telefono)
                        <p>Teléfono: {{ $cliente->telefono }}</p>
                        @endif
                    </div>
                </td>
                @endif
                @if($inmueble)
                <td>
                    <div class="box-text">
                        <p><strong>{{ $inmueble->titulo }}</strong></p>
                        @if($inmueble->ubicacion)
                        <p>Ubicación: {{ $inmueble->ubicacion }}</p>
                        @endif
                        @if($inmueble->cod_postal)
                        <p>Código Postal: {{ $inmueble->cod_postal }}</p>
                        @endif
                        @if($inmueble->valor_referencia)
                        <p>Valor referencia: {{ number_format($inmueble->valor_referencia, 2) }} €</p>
                        @endif
                    </div>
                </td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    @if($evento && $evento->descripcion)
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-100">Descripción de la Cita</th>
            </tr>
            <tr>
                <td>{!! $evento->descripcion !!}</td>
            </tr>
        </table>
    </div>
    @endif

    @if($hoja_firma->observaciones)
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-100">Observaciones</th>
            </tr>
            <tr>
                <td>{{ $hoja_firma->observaciones }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="mt-20">
        <div class="w-100">
            <div class="firma-container" style="max-width: 600px; margin: 0 auto;">
                <h4 class="text-center mb-10">FIRMA DEL CLIENTE</h4>
                @if(isset($ruta_firma_temporal) && $ruta_firma_temporal)
                    {{-- Usar archivo temporal con public_path como en facturas --}}
                    <img src="{{ public_path($ruta_firma_temporal) }}" alt="Firma Cliente" class="firma-imagen">
                @elseif($hoja_firma->firma_cliente && strpos($hoja_firma->firma_cliente, 'data:image') === false)
                    {{-- Si es una ruta de archivo directa --}}
                    <img src="{{ public_path($hoja_firma->firma_cliente) }}" alt="Firma Cliente" class="firma-imagen">
                @else
                    {{-- Sin firma --}}
                    <div style="min-height: 150px;"></div>
                @endif
                <div class="firma-nombre">
                    {{ $hoja_firma->nombre_cliente ?? '________________________' }}
                </div>
            </div>
        </div>
        
        @if($hoja_firma->nombre_agente)
        <div class="mt-20">
            <div class="w-100">
                <div style="text-align: center; padding: 10px; border-top: 1px solid #333;">
                    <strong>Agente:</strong> {{ $hoja_firma->nombre_agente }}
                </div>
            </div>
        </div>
        @endif
        
        <div class="clear"></div>
    </div>

</body>
</html>

