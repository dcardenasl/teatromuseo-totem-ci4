<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\Services as CIBaseServices;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\IncomingRequest;
use Throwable;

/**
 * Single HTTP client for everything the kiosk needs from the BFF
 * (`teatromuseo-bff`, `public-read` seam) — events (Cartelera), catalog
 * items/techniques/categories (Catálogo) and CMS entries (TeatroEscuela).
 *
 * Replaces the old three-layer Hub-client decorator stack, which pointed at
 * Hub routes (`/api/v1/totem/*`) that were never implemented, and which
 * reimplemented every method three times by hand. Caching here is fresh+stale-on-failure
 * (same shape as `teatromuseo-web`'s `WebApiClient`), backed by CI4's cache
 * service instead of hand-rolled file I/O.
 */
final class BffTotemClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $freshTtl;
    private int $staleTtl;
    private ?CURLRequest $client = null;

    public function __construct(
        private readonly CacheInterface $cache,
        ?CURLRequest $client = null,
    ) {
        $baseUrl = env('TOTEM_BFF_BASE_URL');
        $this->baseUrl = is_string($baseUrl) && $baseUrl !== '' ? $baseUrl : 'http://localhost:8188';

        $apiKey = env('TOTEM_BFF_API_KEY');
        $this->apiKey = is_string($apiKey) ? $apiKey : '';

        $freshTtl = getenv('TOTEM_CACHE_TTL_SECONDS');
        $this->freshTtl = is_numeric($freshTtl) ? (int) $freshTtl : 60;

        $staleTtl = getenv('TOTEM_STALE_TTL_SECONDS');
        $this->staleTtl = is_numeric($staleTtl) ? (int) $staleTtl : 86400;

        $this->client = $client;
    }

    /**
     * Cartelera: `sort=agenda` orders upcoming shows soonest-first, then
     * past shows most-recent-first — exactly the "próximas ascendente, y si
     * faltan para completar la pantalla, rellenar con las más recientes"
     * rule the presenter caps to 5. `description` is only in the BFF's
     * DETAIL fields allowlist for events, not the listing one
     * (`EventPublicReadController::LIST_FIELDS`) — it only comes back
     * nested under `localized.description` here.
     */
    public function shows(string $locale): TotemApiResult
    {
        return $this->cached("public-read/{$locale}/events", [
            'sort' => 'agenda',
            'per_page' => 100,
            'fields' => 'id,title,localized,cover_image,slug,next_occurrence_at,last_occurrence_at',
        ], list: true);
    }

    public function show(string $locale, string $idOrSlug): TotemApiResult
    {
        return $this->cached(
            "public-read/{$locale}/events/" . rawurlencode($idOrSlug),
            ['fields' => 'id,title,description,cover_image,gallery_images,slug,occurrences'],
        );
    }

    /** TeatroEscuela: published entries of the `teatroescuela` CMS collection. */
    public function courses(string $locale): TotemApiResult
    {
        return $this->cached("public-read/{$locale}/entries/teatroescuela", [
            'order_by' => 'field:start_date',
            'order_direction' => 'upcoming',
            'per_page' => 100,
        ], list: true);
    }

    public function course(string $locale, string $slug): TotemApiResult
    {
        return $this->cached("public-read/{$locale}/entries/teatroescuela/" . rawurlencode($slug));
    }

    /**
     * Catálogo: published, kiosk-curated (`show_in_totem`) pieces.
     *
     * @param string|null $category slug, e.g. `titeres`/`mascaras`/`payasos`
     */
    public function collectionItems(string $locale, ?string $category = null, ?string $technique = null, int $perPage = 100): TotemApiResult
    {
        $query = ['per_page' => $perPage];
        if ($category !== null) {
            $query['category'] = $category;
        }
        if ($technique !== null) {
            $query['technique'] = $technique;
        }

        return $this->cached("public-read/{$locale}/collection-items", $query, list: true);
    }

    public function collectionItem(string $locale, string $idOrSlug): TotemApiResult
    {
        return $this->cached("public-read/{$locale}/collection-items/" . rawurlencode($idOrSlug));
    }

    /**
     * These two facets are not locale-dependent — `CatalogFacetReader`
     * projects plain, untranslated `name`/`summary` columns, and the BFF
     * route itself carries no `{locale}` segment.
     *
     * @return TotemApiResult list of {id,name,slug,summary,video_url,pdf_file_id,sort_order[,item_count]}
     */
    public function techniques(bool $withCounts = false): TotemApiResult
    {
        return $this->cached('public/catalog/techniques', $withCounts ? ['with_counts' => 1] : [], list: true);
    }

    public function technique(string $idOrSlug): TotemApiResult
    {
        return $this->cached('public/catalog/techniques/' . rawurlencode($idOrSlug));
    }

    /** @return TotemApiResult list of {id,name,slug,icon,short_description,sort_order[,item_count]} */
    public function catalogCategories(bool $withCounts = false): TotemApiResult
    {
        return $this->cached('public/catalog/categories', $withCounts ? ['with_counts' => 1] : [], list: true);
    }

    /**
     * Calls a BFF public-read GET and returns its `data` with fresh/stale/
     * unavailable state attached. Works for both the full `PublicReadEnvelope`
     * shape (`{ok, data, source: {state}}`, events/catalog-items/cms-entries)
     * and the plain `{data, meta}` facet shape (categories/techniques) — both
     * carry the answer under the same `data` key. A `404` on a detail call is
     * a genuine, confirmed-empty answer from the source, not "unavailable".
     *
     * @param array<string, mixed> $query
     */
    private function cached(string $path, array $query = [], bool $list = false): TotemApiResult
    {
        $cacheKey = $this->cacheKey($path, $query);

        /** @var array{data: list<array<string, mixed>>|array<string, mixed>|null}|null $freshEntry */
        $freshEntry = $this->cache->get($cacheKey);
        if ($freshEntry !== null) {
            return TotemApiResult::fresh($freshEntry['data']);
        }

        $response = $this->fetch($path, $query);
        if ($response === null) {
            return $this->fallbackToStale($cacheKey);
        }

        [$status, $body] = $response;

        if ($status === 404) {
            // Confirmed by the source, not a failure: nothing at this slug/id.
            return TotemApiResult::fresh($list ? [] : null);
        }

        if ($status !== 200 || !is_array($body) || !array_key_exists('data', $body)) {
            return $this->fallbackToStale($cacheKey);
        }

        /** @var list<array<string, mixed>>|array<string, mixed>|null $data */
        $data = $body['data'];
        $this->remember($cacheKey, $data);

        return TotemApiResult::fresh($data);
    }

    private function fallbackToStale(string $cacheKey): TotemApiResult
    {
        /** @var array{data: list<array<string, mixed>>|array<string, mixed>|null}|null $staleEntry */
        $staleEntry = $this->cache->get($this->staleCacheKey($cacheKey));
        if ($staleEntry !== null) {
            return TotemApiResult::stale($staleEntry['data']);
        }

        return TotemApiResult::unavailable();
    }

    /** @param list<array<string, mixed>>|array<string, mixed>|null $data */
    private function remember(string $cacheKey, array|null $data): void
    {
        $this->cache->save($cacheKey, ['data' => $data], $this->freshTtl);
        // The stale copy only advances on a real success and outlives the
        // fresh entry by a lot, so an extended BFF outage still has real,
        // recently-true content to fall back to instead of an empty screen.
        $this->cache->save($this->staleCacheKey($cacheKey), ['data' => $data], $this->staleTtl);
    }

    /**
     * @param array<string, mixed> $query
     * @return array{0: int, 1: mixed}|null [status, decoded body], or null on
     *     transport failure (network error, client construction failure).
     */
    private function fetch(string $path, array $query): ?array
    {
        $client = $this->getClient();
        if ($client === null) {
            return null;
        }

        $headers = ['Accept' => 'application/json'];
        if ($this->apiKey !== '') {
            $headers['X-App-Key'] = $this->apiKey;
        }
        $incomingRequestId = $this->incomingRequestId();
        if ($incomingRequestId !== '') {
            $headers['X-Request-ID'] = $incomingRequestId;
        }

        $options = ['headers' => $headers];
        if ($query !== []) {
            $options['query'] = $query;
        }

        $startedAt = microtime(true);
        $maxRetries = 2;
        $attempt = 0;
        $status = 0;
        $raw = null;

        do {
            if ($attempt > 0) {
                usleep((int) (250_000 * (2 ** ($attempt - 1))));
            }

            try {
                $response = $client->get($path, $options);
                $status = $response->getStatusCode();
                $raw = (string) $response->getBody();
            } catch (Throwable) {
                $status = 0;
                $raw = null;
            }

            $attempt++;
        } while (($status === 0 || $status >= 500) && $attempt <= $maxRetries);

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);
        $body = $raw !== null ? json_decode($raw, true) : null;
        $this->log(
            $path,
            $durationMs,
            $status,
            $status === 0 ? 'transport_failure' : ($body === null && $raw !== null && $raw !== 'null' ? 'invalid_json' : null),
        );

        if ($status === 0) {
            return null;
        }

        return [$status, $body];
    }

    private function getClient(): ?CURLRequest
    {
        if ($this->client !== null) {
            return $this->client;
        }

        try {
            // Do not reuse CI4's global CURLRequest singleton: another
            // service may have initialized it with a different base URI.
            $this->client = CIBaseServices::curlrequest([
                // CI4's Services::curlrequest() reads $options['baseURI']
                // (camelCase) to build the initial URI — the wrong key name
                // here silently produces an empty base, and a later relative
                // `get('public-read/...')` call gets misresolved as if
                // "public-read" were the hostname (curl error 6).
                'baseURI' => rtrim($this->baseUrl, '/') . '/api/v1/',
                'timeout' => 5,
            ], null, null, false);
        } catch (Throwable) {
            return null;
        }

        return $this->client;
    }

    private function incomingRequestId(): string
    {
        try {
            $request = service('request');
            if ($request instanceof IncomingRequest) {
                return trim($request->getHeaderLine('X-Request-ID'));
            }
        } catch (Throwable) {
            // No incoming request in this context (e.g. CLI) — nothing to propagate.
        }

        return '';
    }

    /** @param array<string, mixed> $query */
    private function cacheKey(string $path, array $query): string
    {
        ksort($query);

        return 'totem_bff_' . md5($path . '?' . http_build_query($query));
    }

    private function staleCacheKey(string $cacheKey): string
    {
        return $cacheKey . '_stale';
    }

    private function log(string $path, int $durationMs, int $status, ?string $error): void
    {
        $entry = [
            'timestamp' => date('c'),
            'service' => 'bff_totem_client',
            'path' => $path,
            'duration' => $durationMs,
            'status' => $status,
            'success' => $error === null && $status >= 200 && $status < 300,
        ];
        if ($error !== null) {
            $entry['error'] = $error;
        }

        $level = $error !== null || $status >= 400 ? 'warning' : 'debug';
        log_message($level, '[BffTotemClient] ' . json_encode($entry, JSON_UNESCAPED_SLASHES));
    }
}
