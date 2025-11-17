<?php

namespace App\Services\Idealista;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use RuntimeException;

class IdealistaClient
{
    public function __construct(
        private readonly HttpFactory $httpFactory,
        private readonly CacheRepository $cache,
        private array $config = []
    ) {
        $this->config = $this->config ?: config('services.idealista', []);

        if (empty($this->config['client_id']) || empty($this->config['client_secret'])) {
            throw new RuntimeException('Faltan las credenciales de Idealista en services.idealista.');
        }
    }

    public function getAccessToken(bool $forceRefresh = false): string
    {
        $cacheKey = $this->cacheKey();

        if (! $forceRefresh) {
            $cachedToken = $this->cache->get($cacheKey);
            if ($cachedToken) {
                return $cachedToken;
            }
        }

        $formParams = ['grant_type' => 'client_credentials'];

        if (! empty($this->config['scope'])) {
            $formParams['scope'] = $this->config['scope'];
        }

        $response = $this->httpFactory
            ->asForm()
            ->baseUrl($this->baseUrl())
            ->withHeaders([
                'Authorization' => 'Basic '.$this->buildBasicCredentials(),
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            ])
            ->withOptions(['verify' => $this->verifyOption()])
            ->timeout($this->timeout())
            ->post('/oauth/token', $formParams);

        Log::debug('[Idealista] Token request result', [
            'url' => $this->baseUrl().'/oauth/token',
            'status' => $response->status(),
            'scope' => $formParams['scope'] ?? null,
        ]);

        $response->throw();

        $payload = $response->json();

        Log::debug('[Idealista] Token payload keys', [
            'keys' => array_keys($payload ?? []),
        ]);

        $token = Arr::get($payload, 'access_token');

        if (! $token) {
            throw new RuntimeException('Idealista no devolvió un access_token.');
        }

        $expiresIn = (int) Arr::get($payload, 'expires_in', 0);
        $ttl = $this->ttlFromExpires($expiresIn);

        if ($ttl > 0) {
            $this->cache->put($cacheKey, $token, $ttl);
        }

        return $token;
    }

    public function request(string $method, string $endpoint, array $options = [])
    {
        $method = strtoupper($method);
        $endpoint = '/'.ltrim($endpoint, '/');

        try {
            $response = $this->http()->send($method, $endpoint, $options);
        } catch (RequestException $exception) {
            throw $exception;
        }

        if ($response->status() === 401) {
            $response = $this->http(true)->send($method, $endpoint, $options);
        }

        return $response;
    }

    private function http(bool $forceRefresh = false): PendingRequest
    {
        $token = $this->getAccessToken($forceRefresh);

        return $this->httpFactory
            ->baseUrl($this->baseUrl())
            ->withToken($token, 'Bearer')
            ->withOptions(['verify' => $this->verifyOption()])
            ->timeout($this->timeout());
    }

    private function baseUrl(): string
    {
        $template = $this->config['host_template'] ?? 'https://partners-sandbox.idealista.%s';
        $country = $this->config['country'] ?? 'com';

        return rtrim(sprintf($template, $country), '/');
    }

    private function buildBasicCredentials(): string
    {
        if (! empty($this->config['basic_token'])) {
            return trim($this->config['basic_token']);
        }

        $clientId = rawurlencode(trim((string) ($this->config['client_id'] ?? '')));
        $clientSecret = rawurlencode(trim((string) ($this->config['client_secret'] ?? '')));

        return base64_encode("{$clientId}:{$clientSecret}");
    }

    private function timeout(): int
    {
        return (int) ($this->config['timeout'] ?? 15);
    }

    private function ttlFromExpires(int $expiresIn): int
    {
        if ($expiresIn <= 0) {
            return 0;
        }

        $buffer = min(60, (int) ($expiresIn * 0.1));

        return max($expiresIn - $buffer, 1);
    }

    private function cacheKey(): string
    {
        $hash = md5($this->config['client_id'] ?? 'idealista');

        return "idealista:access_token:{$hash}";
    }

    private function verifyOption(): mixed
    {
        if (! array_key_exists('verify_ssl', $this->config)) {
            return true;
        }

        $verify = $this->config['verify_ssl'];

        if (is_string($verify)) {
            $normalized = strtolower($verify);

            if (in_array($normalized, ['false', '0', 'no'], true)) {
                return false;
            }

            if (in_array($normalized, ['true', '1', 'yes'], true)) {
                return true;
            }
        }

        return $verify;
    }
}

