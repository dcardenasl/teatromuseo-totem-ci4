<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;

/**
 * Stands in for the BFF over HTTP in tests — `BffTotemClient` only ever
 * calls `get($path, $options)` on its injected client, so overriding that
 * one method is enough to drive every controller/presenter test without a
 * real `teatromuseo-bff` process.
 */
final class FakeBffCurlRequest extends CURLRequest
{
    /** @var array<string, list<array{status:int, body:mixed}>> */
    private readonly array $sequences;

    /** @var list<array{url:string, options:array<string, mixed>}> */
    private array $requests = [];

    /** @var array<string, int> */
    private array $sequenceIndexes = [];

    /**
     * @param array<string, array{status:int, body:mixed}> $responses keyed by request path (no query string)
     * @param bool $failTransport simulate a network failure (BFF unreachable) on every call, regardless of $responses
     * @param array<string, list<array{status:int, body:mixed}>> $sequences optional ordered responses per request path
     */
    public function __construct(
        private readonly array $responses = [],
        private readonly bool $failTransport = false,
        array $sequences = [],
    ) {
        $this->sequences = $sequences;
    }

    public function get(string $url, array $options = []): ResponseInterface
    {
        $this->requests[] = ['url' => $url, 'options' => $options];

        if ($this->failTransport) {
            throw new \RuntimeException('Simulated transport failure: BFF unreachable.');
        }

        if (isset($this->sequences[$url])) {
            $index = $this->sequenceIndexes[$url] ?? 0;
            $entries = $this->sequences[$url];
            $entry = $entries[min($index, count($entries) - 1)];
            $this->sequenceIndexes[$url] = $index + 1;
        } else {
            $entry = $this->responses[$url] ?? ['status' => 404, 'body' => ['data' => null]];
        }

        $response = new Response(new App());
        $response->setStatusCode($entry['status']);
        $response->setBody((string) json_encode($entry['body']));

        return $response;
    }

    public function callCount(string $url): int
    {
        return count(array_filter($this->requests, static fn (array $request): bool => $request['url'] === $url));
    }

    /** @return list<array{url:string, options:array<string, mixed>}> */
    public function requests(): array
    {
        return $this->requests;
    }

    /**
     * A full `PublicReadEnvelope` success body (events/collection-items/cms-entries).
     *
     * @param list<array<string, mixed>>|array<string, mixed>|null $data
     * @return array{status:int, body:mixed}
     */
    public static function envelope(array|null $data, string $state = 'fresh'): array
    {
        return ['status' => 200, 'body' => [
            'version' => 1,
            'ok' => true,
            'data' => $data,
            'meta' => ['locale' => 'es'],
            'source' => ['domain' => 'test', 'state' => $state, 'stale' => $state === 'stale'],
            'messages' => [],
        ]];
    }

    /**
     * A plain `{data, meta}` facet body (catalog categories/techniques).
     *
     * @param list<array<string, mixed>>|array<string, mixed>|null $data
     * @return array{status:int, body:mixed}
     */
    public static function facet(array|null $data): array
    {
        return ['status' => 200, 'body' => ['data' => $data, 'meta' => ['generated_at' => date(DATE_ATOM)]]];
    }

    /** @return array{status:int, body:mixed} */
    public static function notFound(): array
    {
        return ['status' => 404, 'body' => ['data' => null]];
    }
}
