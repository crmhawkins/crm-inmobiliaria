<!DOCTYPE html>
<html>

<head>
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

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">Factura</h1>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Número de factura - <span
                    class="gray-color">{{ $factura['numero_factura'] }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Fecha de emisión - <span class="gray-color">22-01-2023</span></p>
            <p class="m-0 pt-5 text-bold w-100">Fecha de vencimiento - <span class="gray-color">22-01-2023</span></p>
        </div>
        <div class="w-50 float-left logo mt-10">
            <img class="img-fluid" src="{{ public_path('images/logosayco.png') }}" alt="Logo">
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">De</th>
                <th class="w-50">Para</th>
            </tr>
            <tr>
                <td>
                    <div class="box-text">
                        <p>Inmobiliaria x</p>
                        <p>NIF</p>
                        <p>Dirección</p>
                        <p>Teléfono</p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p><strong>{{ $factura['cliente_nombre'] }}</strong></p>
                        <p>{{ $factura['cliente_dni'] }}</p>
                        <p>United States</p>
                        <p>Contact: 1-206-266-1000</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-100">Condiciones y métodos de pago</th>
            </tr>
            <tr>
                <td>{{ $factura['condiciones'] }}</td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">#</th>
                <th class="w-50">Artículo</th>
                <th class="w-50">Precio</th>
                <th class="w-50">IVA</th>
                <th class="w-50">Total</th>
            </tr>
            @foreach (json_decode($factura['articulos'], true) as $id => $item)
                <tr>
                    <td>{{ $id }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td>{{ $item['importe'] }} €</td>
                    <td>0,{{ $item['impuesto'] }} %</td>
                    <td>{{ $item['importe'] * ($item['impuesto'] / 100) }} €</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7">
                    <div class="total-part">
                        <div class="total-left w-85 float-left" align="right">
                            <p>Subtotal</p>
                            <p>Impuestos</p>
                            <p>Total</p>
                        </div>
                        <div class="total-right w-15 float-left text-bold" align="right">
                            <p>{{ $factura['subtotal'] }} €</p>
                            <p>{{ $factura['total'] - $factura['subtotal'] }} €</p>
                            <p>{{ $factura['total'] }} €</p>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</html>
