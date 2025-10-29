<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Propiedad - {{ $inmobiliaria ?? 'Inmobiliaria' }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .property-card {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .property-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .property-details {
            margin: 15px 0;
        }
        .property-details-item {
            margin: 8px 0;
            color: #555;
        }
        .property-details-item strong {
            color: #2c3e50;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $inmobiliaria ?? 'Inmobiliaria' }}</h1>
    </div>
    
    <div class="content">
        <h2>Estimado/a {{ $cliente->nombre_completo }},</h2>
        
        <p>Nos complace informarle que hemos añadido una nueva propiedad a nuestro catálogo que podría encajar con sus intereses.</p>
        
        <div class="property-card">
            <div class="property-title">{{ $inmueble->titulo }}</div>
            
            <p style="color: #555; margin: 10px 0;">{{ Str::limit($inmueble->descripcion, 200) }}</p>
            
            <div class="property-details">
                @if($inmueble->ubicacion)
                <div class="property-details-item">
                    <strong>📍 Ubicación:</strong> {{ $inmueble->ubicacion }}
                </div>
                @endif
                
                @if($inmueble->habitaciones)
                <div class="property-details-item">
                    <strong>🛏️ Habitaciones:</strong> {{ $inmueble->habitaciones }}
                </div>
                @endif
                
                @if($inmueble->banos)
                <div class="property-details-item">
                    <strong>🚿 Baños:</strong> {{ $inmueble->banos }}
                </div>
                @endif
                
                @if($inmueble->m2)
                <div class="property-details-item">
                    <strong>📐 Superficie:</strong> {{ $inmueble->m2 }} m²
                </div>
                @endif
                
                @if($inmueble->valor_referencia)
                <div class="property-details-item">
                    <strong>💰 Precio referencia:</strong> {{ number_format($inmueble->valor_referencia, 2, ',', '.') }} €
                </div>
                @endif
                
                @if($inmueble->estado)
                <div class="property-details-item">
                    <strong>🏠 Estado:</strong> {{ $inmueble->estado }}
                </div>
                @endif
                
                @if($inmueble->disponibilidad)
                <div class="property-details-item">
                    <strong>✅ Disponibilidad:</strong> {{ $inmueble->disponibilidad }}
                </div>
                @endif
            </div>
        </div>
        
        <p>Si está interesado en esta propiedad, no dude en contactarnos para más información o para agendar una visita.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('inmueble.public.show', $inmueble->id) }}" class="button">Ver Detalles de la Propiedad</a>
        </div>
        
        <p style="margin-top: 20px; font-size: 14px; color: #6c757d;">
            Este correo se le ha enviado porque tenemos registrado que está interesado en propiedades con características similares. 
            Si no desea recibir más alertas, puede ponerse en contacto con nosotros.
        </p>
    </div>
    
    <div class="footer">
        <p>{{ $inmobiliaria ?? 'Inmobiliaria' }}</p>
        <p>Este es un correo automático, por favor no responda a este mensaje.</p>
    </div>
</body>
</html>

