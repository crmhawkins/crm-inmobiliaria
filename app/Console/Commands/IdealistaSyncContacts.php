<?php

namespace App\Console\Commands;

use App\Models\Clientes;
use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Throwable;

class IdealistaSyncContacts extends Command
{
    protected $signature = 'idealista:sync-contacts
        {--page=1 : Página inicial}
        {--size=50 : Registros por página (máx. 100)}
        {--all : Recorre todas las páginas disponibles}
        {--dry-run : Solo muestra los contactos, no guarda}';

    protected $description = 'Sincroniza los contactos de Idealista con la tabla clientes';

    private int $created = 0;
    private int $updated = 0;
    private int $skipped = 0;

    public function __construct(private readonly IdealistaApiService $api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $page = max(1, (int) $this->option('page'));
        $size = max(1, min(100, (int) $this->option('size')));
        $all = (bool) $this->option('all');
        $dryRun = (bool) $this->option('dry-run');

        do {
            $response = $this->fetchPage($page, $size);
            if ($response === null) {
                break;
            }

            $contacts = $response['contacts'] ?? [];
            if (empty($contacts)) {
                $this->warn("Página {$page} sin resultados");
                break;
            }

            foreach ($contacts as $contact) {
                $this->syncContact($contact, $dryRun);
            }

            $this->info(sprintf(
                'Página %d procesada (%d contactos)',
                $page,
                count($contacts)
            ));

            $page++;
        } while ($all);

        $this->line('');
        $this->info('Resumen contactos');
        $this->line("  Nuevos: {$this->created}");
        $this->line("  Actualizados: {$this->updated}");
        $this->line("  Omitidos: {$this->skipped}");

        return self::SUCCESS;
    }

    private function fetchPage(int $page, int $size): ?array
    {
        $attempts = 0;

        while ($attempts < 5) {
            try {
                return $this->api->call('GET', '/v1/contacts', [
                    'page' => $page,
                    'size' => $size,
                ]);
            } catch (RequestException $exception) {
                if ($exception->response && $exception->response->status() === 429) {
                    $sleep = 2 ** $attempts;
                    $this->warn("Rate limit alcanzado. Reintentando en {$sleep}s...");
                    sleep($sleep);
                    $attempts++;
                    continue;
                }

                throw $exception;
            } catch (Throwable $exception) {
                $this->error("No se pudo obtener la página {$page}: {$exception->getMessage()}");

                return null;
            }
        }

        $this->error("Se excedieron los reintentos para la página {$page}");

        return null;
    }

    private function syncContact(array $contact, bool $dryRun): void
    {
        $contactId = Arr::get($contact, 'contactId');

        if (! $contactId) {
            $this->warn('Contacto sin contactId. Omitido.');
            $this->skipped++;

            return;
        }

        $data = [
            'nombre_completo' => $this->buildName($contact),
            'email' => Arr::get($contact, 'email'),
            'telefono' => Arr::get($contact, 'primaryPhoneNumber'),
            'telefono_prefijo' => $this->normalizePrefix(Arr::get($contact, 'primaryPhonePrefix')),
            'idealista_contact_id' => $contactId,
            'idealista_es_agente' => (bool) Arr::get($contact, 'agent', false),
            'idealista_es_activo' => (bool) Arr::get($contact, 'active', true),
        ];

        if ($dryRun) {
            $this->line(sprintf('[DRY-RUN] Contacto %s (%s)', $data['nombre_completo'], $contactId));
            $this->skipped++;

            return;
        }

        $cliente = Clientes::where('idealista_contact_id', $contactId)->first();

        if ($cliente) {
            $cliente->fill($data);
            $cliente->save();
            $this->updated++;

            return;
        }

        Clientes::create($data);
        $this->created++;
    }

    private function buildName(array $contact): string
    {
        $name = trim(($contact['name'] ?? '').' '.($contact['lastName'] ?? ''));

        if ($name !== '') {
            return $name;
        }

        return $contact['email'] ?? "Contacto {$contact['contactId']}";
    }

    private function normalizePrefix(?string $prefix): ?string
    {
        if (! $prefix) {
            return null;
        }

        $prefix = trim($prefix);

        if ($prefix === '') {
            return null;
        }

        return str_starts_with($prefix, '+') ? $prefix : '+'.$prefix;
    }
}

