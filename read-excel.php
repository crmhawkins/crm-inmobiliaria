<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $data = \Maatwebsite\Excel\Facades\Excel::load('Copia de IDEALISTA test-cases.xlsx')->get();

    echo "Total rows: " . $data->count() . "\n\n";

    $rows = $data->toArray();

    // Mostrar las primeras filas
    foreach ($rows as $index => $row) {
        echo "Row " . ($index + 1) . ":\n";
        print_r($row);
        echo "\n---\n\n";

        if ($index >= 10) {
            echo "... (showing first 10 rows)\n";
            break;
        }
    }

    // Guardar en JSON para inspección
    file_put_contents('excel-content.json', json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\nContent saved to excel-content.json\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

