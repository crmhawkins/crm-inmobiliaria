<!DOCTYPE html>
<html>
<head>
    <title>Hoja de Visita</title>
    <style>
        @page {
            margin: 2cm;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2ecc71;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #2ecc71;
            margin: 10px 0;
            font-size: 24px;
        }

        .header p {
            color: #666;
            margin: 5px 0;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            padding: 10px 15px;
            margin: 0 -15px 15px -15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 200px;
        }

        table td {
            background-color: white;
        }

        .signature-box {
            border: 2px solid #2ecc71;
            padding: 30px;
            margin-top: 30px;
            border-radius: 8px;
            text-align: center;
            background: #f8f9fa;
        }

        .signature-box img {
            max-width: 400px;
            max-height: 200px;
            border: 1px solid #ddd;
            padding: 10px;
            background: white;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }

        .badge-success {
            background-color: #2ecc71;
            color: white;
        }

        .badge-info {
            background-color: #3498db;
            color: white;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>HOJA DE VISITA</h1>
        <p>Documento oficial de visita a inmueble</p>
        <p>Fecha: {{ $fecha }}</p>
    </div>

    <!-- Información del Cliente -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-user-circle"></i> INFORMACIÓN DEL CLIENTE
        </div>
        <table>
            <tr>
                <th>Nombre completo</th>
                <td><strong>{{ $cliente->nombre_completo }}</strong></td>
            </tr>
            <tr>
                <th>DNI/NIF</th>
                <td>{{ $cliente->dni }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $cliente->email }}</td>
            </tr>
            @if($cliente->telefono)
            <tr>
                <th>Teléfono</th>
                <td>{{ $cliente->telefono }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Información del Inmueble -->
    <div class="section">
        <div class="section-title">
            <i class="fas fa-home"></i> INFORMACIÓN DEL INMUEBLE
        </div>
        <table>
            <tr>
                <th>Título</th>
                <td><strong>{{ $inmueble->titulo }}</strong></td>
            </tr>
            <tr>
                <th>Ubicación</th>
                <td>{{ $inmueble->ubicacion }}</td>
            </tr>
            <tr>
                <th>Tipo de vivienda</th>
                <td>
                    @php
                        $tipo = \App\Models\TipoVivienda::find($inmueble->tipo_vivienda_id);
                    @endphp
                    {{ $tipo ? $tipo->nombre : 'N/A' }}
                </td>
            </tr>
            <tr>
                <th>Referencia catastral</th>
                <td>{{ $inmueble->referencia_catastral ?? 'N/A' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th>Metros cuadrados</th>
                <td>{{ $inmueble->m2 }} m²</td>
            </tr>
            <tr>
                <th>Metros construidos</th>
                <td>{{ $inmueble->m2_construidos ?? $inmueble->m2 }} m²</td>
            </tr>
            <tr>
                <th>Habitaciones</th>
                <td>{{ $inmueble->habitaciones }}</td>
            </tr>
            <tr>
                <th>Baños</th>
                <td>{{ $inmueble->banos }}</td>
            </tr>
        </table>

        @if($inmueble->valor_referencia)
        <table>
            <tr>
                <th>Precio de referencia</th>
                <td class="highlight"><strong>{{ number_format($inmueble->valor_referencia, 2, ',', '.') }} €</strong></td>
            </tr>
            <tr>
                <th>Tipo de operación</th>
                <td><span class="badge badge-info">{{ $inmueble->disponibilidad }}</span></td>
            </tr>
        </table>
        @endif
    </div>

    <!-- Firma del Cliente -->
    <div class="signature-box">
        <h3 style="margin-top: 0; color: #2ecc71;">FIRMA DEL CLIENTE</h3>
        <p style="margin-bottom: 20px; color: #666;">
            El cliente certifica haber visitado el inmueble descrito en esta hoja
        </p>
        @if($firma_path && file_exists(public_path($firma_path)))
            <img src="{{ public_path($firma_path) }}" alt="Firma del cliente">
        @else
            <p style="color: #999;">Firma no disponible</p>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Este documento ha sido generado automáticamente el {{ date('d/m/Y H:i:s') }}</p>
        <p>© {{ date('Y') }} - Sistema de Gestión Inmobiliaria</p>
    </div>
</body>
</html>

