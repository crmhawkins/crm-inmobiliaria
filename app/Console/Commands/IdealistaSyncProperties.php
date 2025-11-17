<?php

namespace App\Console\Commands;

use App\Models\Clientes;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Services\Idealista\IdealistaPropertiesService;
use App\Services\Idealista\IdealistaPropertyMapper;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Throwable;

class IdealistaSyncProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:sync-properties
        {--page=1 : Página inicial a solicitar}
        {--pages=1 : Número de páginas consecutivas a sincronizar}
        {--all : Ignora pages y recorre todas las páginas disponibles}
        {--size=50 : Número de registros por página (máx. 100)}
        {--state= : Filtra por estado (active, inactive, pending, dropped_by_quality). Por defecto: active}
        {--dry-run : Ejecuta la sincronización sin guardar cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Descarga propiedades activas del sandbox de Idealista y las guarda en la tabla inmuebles';

    private array $tipoCache = [];

    public function __construct(
        private readonly IdealistaPropertiesService $service,
        private readonly IdealistaPropertyMapper $mapper
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! config('services.idealista.feed_key')) {
            $this->error('Configura la variable IDEALISTA_FEED_KEY antes de sincronizar.');

            return self::FAILURE;
        }

        $page = max(1, (int) $this->option('page'));
        $pages = max(1, (int) $this->option('pages'));
        $size = max(1, min(100, (int) $this->option('size')));
        // Por defecto solo sincronizar inmuebles activos
        $state = $this->option('state') ?: 'active';
        $dryRun = (bool) $this->option('dry-run');
        $all = (bool) $this->option('all');

        $summary = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        $currentPage = $page;

        do {
            $response = $this->fetchPage($currentPage, $size, $state);
            if ($response === null) {
                return self::FAILURE;
            }

            $properties = $response['properties'] ?? [];

            if (empty($properties)) {
                $this->warn("La página {$currentPage} no devolvió propiedades. Proceso detenido.");
                break;
            }

            foreach ($properties as $property) {
                $result = $this->syncProperty($property, $dryRun);
                $summary[$result] = ($summary[$result] ?? 0) + 1;
            }

            $pageInfo = $response['page'] ?? [];
            $this->info(sprintf(
                'Página %d/%s procesada (%d propiedades).',
                $pageInfo['number'] ?? $currentPage,
                $pageInfo['total'] ?? '?',
                count($properties)
            ));

            $currentPage++;
        } while ($all || $currentPage < ($page + $pages));

        $this->line('');
        $this->info('Resumen de sincronización');
        $this->line("  Nuevos: {$summary['created']}");
        $this->line("  Actualizados: {$summary['updated']}");
        $this->line("  Omitidos: {$summary['skipped']}");

        if ($dryRun) {
            $this->comment('Modo DRY-RUN: no se han realizado escrituras en la base de datos.');
        }

        return self::SUCCESS;
    }

    private function syncProperty(array $property, bool $dryRun): string
    {
        // Obtener imágenes de la propiedad
        $images = null;
        $propertyId = Arr::get($property, 'propertyId');

        if ($propertyId && !$dryRun) {
            try {
                $images = $this->service->listImages($propertyId);
                // Si la respuesta es un array con una clave que contiene las imágenes, extraerlas
                if (is_array($images) && isset($images['images'])) {
                    $images = $images['images'];
                } elseif (is_array($images) && isset($images['data'])) {
                    $images = $images['data'];
                }
            } catch (Throwable $e) {
                // Si falla obtener las imágenes, continuar sin ellas
                $this->warn("No se pudieron obtener imágenes para la propiedad {$propertyId}: {$e->getMessage()}");
                $images = null;
            }
        }

        $mapped = $this->mapper->map($property, $images);
        $attributes = $mapped['attributes'];

        if (! empty($mapped['tipo_vivienda_label'])) {
            $tipoId = $this->resolveTipoViviendaId($mapped['tipo_vivienda_label'], ! $dryRun);
            if ($tipoId) {
                $attributes['tipo_vivienda_id'] = $tipoId;
            }
        }

        foreach (['transaction_type_id', 'visibility_mode_id', 'floor_id', 'orientation_id'] as $key) {
            if (array_key_exists($key, $mapped) && $mapped[$key] !== null) {
                $attributes[$key] = $mapped[$key];
            }
        }

        $attributes['idealista_synced_at'] = now();

        $identifier = $this->buildIdentifier($property);
        if (! $identifier) {
            $this->warn('Propiedad sin identificadores válidos (propertyId/code). Se omite.');

            return 'skipped';
        }

        if ($dryRun) {
            $slug = [];
            foreach ($identifier as $key => $value) {
                $slug[] = "{$key}:{$value}";
            }

            $this->line(sprintf(
                '[DRY-RUN] %s (%s)',
                $attributes['titulo'] ?? 'Sin título',
                implode(', ', $slug)
            ));

            return 'skipped';
        }

        if ($contactId = Arr::get($property, 'contactId')) {
            $contact = $this->resolveContact($contactId, $dryRun);
            if ($contact) {
                $attributes['vendedor_id'] = $contact->id;
            }
        }

        $inmueble = Inmuebles::where($identifier)->first();

        if ($inmueble) {
            $inmueble->fill($attributes);
            $inmueble->save();

            return 'updated';
        }

        Inmuebles::create($attributes + $identifier);

        return 'created';
    }

    private function buildIdentifier(array $property): ?array
    {
        if (! empty($property['propertyId'])) {
            return ['idealista_property_id' => $property['propertyId']];
        }

        if (! empty($property['code'])) {
            return ['idealista_code' => $property['code']];
        }

        return null;
    }

    private function resolveTipoViviendaId(string $label, bool $allowCreate): ?int
    {
        if (isset($this->tipoCache[$label])) {
            return $this->tipoCache[$label];
        }

        $record = TipoVivienda::where('nombre', $label)->first();

        if (! $record && $allowCreate) {
            $record = TipoVivienda::create(['nombre' => $label]);
        }

        return $this->tipoCache[$label] = $record?->id;
    }

    private function resolveContact(int $contactId, bool $dryRun): ?Clientes
    {
        if ($dryRun) {
            return null;
        }

        $contact = Clientes::where('idealista_contact_id', $contactId)->first();
        if ($contact) {
            return $contact;
        }

        $contact = Clientes::create([
            'idealista_contact_id' => $contactId,
            'nombre_completo' => "Contacto Idealista {$contactId}",
        ]);

        $this->warn("Contacto {$contactId} no existía y se creó con datos mínimos. Ejecuta idealista:sync-contacts para completarlo.");

        return $contact;
    }

    private function fetchPage(int $page, int $size, ?string $state): ?array
    {
        $attempts = 0;

        while ($attempts < 5) {
            try {
                return $this->service->list($page, $size, $state);
            } catch (RequestException $exception) {
                if ($exception->response && $exception->response->status() === 429) {
                    $sleep = 2 ** $attempts;
                    $this->warn("Rate limit alcanzado en page {$page}. Reintentando en {$sleep}s...");
                    sleep($sleep);
                    $attempts++;
                    continue;
                }

                $this->error("Error obteniendo la página {$page}: {$exception->getMessage()}");

                return null;
            } catch (Throwable $exception) {
                $this->error("Error obteniendo la página {$page}: {$exception->getMessage()}");

                return null;
            }
        }

        $this->error("Se excedieron los reintentos para la página {$page}");

        return null;
    }
}

