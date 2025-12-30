<?php
// Script temporal para parsear el WSDL de GetProperty

$wsdlUrl = 'http://ws.fotocasa.es/mobile/api/v3.asmx?WSDL';
echo "Descargando WSDL...\n";
$wsdl = file_get_contents($wsdlUrl);
echo "WSDL descargado (" . strlen($wsdl) . " bytes)\n\n";

// Buscar la definición de GetPropertyRequest
echo "=== Buscando GetPropertyRequest ===\n";
if (preg_match('/<s:element name="GetPropertyRequest"[^>]*>(.*?)<\/s:element>/s', $wsdl, $matches)) {
    echo "GetPropertyRequest encontrado:\n";
    echo $matches[0] . "\n\n";
} else {
    echo "No se encontró GetPropertyRequest\n\n";
}

// Buscar cualquier elemento que contenga "GetProperty"
echo "=== Buscando todos los elementos GetProperty ===\n";
if (preg_match_all('/<s:element[^>]*name="[^"]*GetProperty[^"]*"[^>]*>.*?<\/s:element>/s', $wsdl, $matches)) {
    foreach ($matches[0] as $i => $elem) {
        echo "Elemento " . ($i + 1) . ":\n";
        echo substr($elem, 0, 2000) . "\n\n";
    }
} else {
    echo "No se encontraron elementos GetProperty\n\n";
}

// Buscar mensajes
echo "=== Buscando mensajes GetProperty ===\n";
if (preg_match_all('/<wsdl:message name="[^"]*GetProperty[^"]*">(.*?)<\/wsdl:message>/s', $wsdl, $matches)) {
    foreach ($matches[0] as $i => $msg) {
        echo "Mensaje " . ($i + 1) . ":\n";
        echo $msg . "\n\n";
    }
} else {
    echo "No se encontraron mensajes GetProperty\n\n";
}

// Buscar operaciones
echo "=== Buscando operación GetProperty ===\n";
if (preg_match('/<wsdl:operation name="GetProperty">(.*?)<\/wsdl:operation>/s', $wsdl, $matches)) {
    echo "Operación encontrada:\n";
    echo $matches[0] . "\n\n";
} else {
    echo "No se encontró la operación\n\n";
}

// Buscar tipos complejos que puedan contener parámetros
echo "=== Buscando tipos que contengan propertyId o ExternalId ===\n";
if (preg_match_all('/<s:element[^>]*name="(propertyId|PropertyId|externalId|ExternalId|Id|id)"[^>]*>.*?<\/s:element>/s', $wsdl, $matches)) {
    foreach ($matches[0] as $i => $elem) {
        echo "Elemento " . ($i + 1) . " (" . $matches[1][$i] . "):\n";
        echo $elem . "\n\n";
    }
} else {
    echo "No se encontraron elementos con esos nombres\n\n";
}
