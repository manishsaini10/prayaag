<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class GraphApiService
{
    private string $baseUrl;

    public function __construct(
        private readonly int $maxRetries = 3,
    ) {
        $this->baseUrl = 'https://graph.facebook.com/' . config('instagram.graph_version', 'v23.0');
    }

    public function get(string $endpoint, array $params = []): array
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function delete(string $endpoint, array $params = []): array
    {
        return $this->request('DELETE', $endpoint, $params);
    }

    public function concurrent(array $requests): array
    {
        $responses = Http::pool(function (Pool $pool) use ($requests) {
            return array_map(fn(array $req) => $pool->as($req['key'] ?? uniqid())
                ->withOptions(['timeout' => 30])
                ->get($this->baseUrl . '/' . $req['endpoint'], $req['params'] ?? []),
                $requests
            );
        });

        return array_map(fn(Response $r) => $r->json() ?? [], $responses);
    }

    private function request(string $method, string $endpoint, array $data = []): array
    {
        $attempts = 0;

        while ($attempts < $this->maxRetries) {
            $attempts++;

            $response = Http::withOptions([
                'timeout' => 30,
                'http_errors' => false,
            ])->$method($this->baseUrl . '/' . $endpoint, $data);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            $status = $response->status();
            $body = $response->json();
            $errorCode = $body['error']['code'] ?? null;
            $errorMsg = $body['error']['message'] ?? 'Unknown';

            Log::channel(config('instagram.log_channel'))->warning(
                "Graph API request failed (attempt {$attempts}/{$this->maxRetries}): {$errorMsg}",
                ['endpoint' => $endpoint, 'status' => $status, 'code' => $errorCode]
            );

            // Non-retryable errors
            if (in_array($errorCode, [100, 200, 190, 10, 4], true)) {
                throw new \RuntimeException("Graph API error [{$errorCode}]: {$errorMsg}");
            }

            if ($attempts >= $this->maxRetries) {
                throw new \RuntimeException("Graph API failed after {$this->maxRetries} attempts: {$errorMsg}");
            }

            sleep(min($attempts * 2, 10));
        }

        throw new \RuntimeException('Graph API request failed unexpectedly');
    }
}
