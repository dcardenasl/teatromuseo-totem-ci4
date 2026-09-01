<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use App\Commands\WarmBffCache;
use App\Services\BffTotemClient;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;
use ReflectionClass;
use Tests\Support\FakeBffCurlRequest;

/**
 * `TOTEM-BFF-10`/`TOTEM-BFF-18`/`TOTEM-BFF-19`: guards the background cache
 * warm-up command against regressions — calling something other than the
 * exact listing/facet methods the screens use (which would either warm the
 * wrong keys or start an unbounded per-item fan-out ADR-010 forbids), losing
 * its strictly sequential execution order (no `curl_multi`, no concurrency
 * primitives), and losing either of its two deliberate, still-bounded
 * exceptions: Cartelera's up-to-5 featured events' detail pages, and the
 * whole Catálogo (collection items + técnicas) seeded from bounded bulk
 * listing calls instead of one HTTP call per piece.
 *
 * @internal
 */
final class WarmBffCacheTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Services::cache()->clean();
    }

    public function testWarmsEveryListingAndFacetCallAcrossAllLocalesExactlyOnce(): void
    {
        $fake = new FakeBffCurlRequest($this->responsesForEveryExpectedCall());
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), $fake));

        $this->runCommand();

        foreach (['es', 'en', 'fr', 'pt'] as $locale) {
            $this->assertSame(1, $fake->callCount("public-read/{$locale}/events"), "shows({$locale}) should be called exactly once");
            $this->assertSame(1, $fake->callCount("public-read/{$locale}/entries/teatroescuela"), "courses({$locale}) should be called exactly once");
            // Same path for the 3 categories x 2 field sets (lean listing +
            // TOTEM-BFF-19's detailed bulk fetch) — the category and fields
            // both live in the query string, not the path — so exactly 6
            // calls land on this one URL per locale.
            $this->assertSame(
                6,
                $fake->callCount("public-read/{$locale}/collection-items"),
                "collectionItems({$locale}) should be called once per category, twice (lean + detailed)",
            );
        }

        $this->assertSame(1, $fake->callCount('public/catalog/techniques'));
        $this->assertSame(1, $fake->callCount('public/catalog/categories'));

        // 4 locales x (shows + courses + 3 categories x 2) + techniques + categories = 34 calls.
        $this->assertCount(34, $fake->requests());
    }

    public function testNeverCallsPerSlugDetailMethodsBeyondTheFeaturedEvents(): void
    {
        $fake = new FakeBffCurlRequest($this->responsesForEveryExpectedCall());
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), $fake));

        $this->runCommand();

        foreach ($fake->requests() as $request) {
            $this->assertStringNotContainsString('/entries/teatroescuela/', $request['url']);
            $this->assertMatchesRegularExpression(
                '#^(public-read/[a-z]{2}/(events(/[\w-]+)?|entries/teatroescuela|collection-items)|public/catalog/(techniques|categories))$#',
                $request['url'],
                'warm-up may only hit event detail endpoints (bounded to the featured events) or listing/facet calls',
            );
        }
    }

    public function testWarmsDetailPagesForCartelerasFeaturedEventsOnly(): void
    {
        $responses = $this->responsesForEveryExpectedCall();
        foreach (['es', 'en', 'fr', 'pt'] as $locale) {
            $responses["public-read/{$locale}/events"] = FakeBffCurlRequest::envelope([
                ['id' => 1, 'slug' => "evento-1-{$locale}", 'title' => 'Evento uno'],
                ['id' => 2, 'slug' => "evento-2-{$locale}", 'title' => 'Evento dos'],
            ]);
            $responses["public-read/{$locale}/events/evento-1-{$locale}"] = FakeBffCurlRequest::envelope(['title' => 'Evento uno']);
            $responses["public-read/{$locale}/events/evento-2-{$locale}"] = FakeBffCurlRequest::envelope(['title' => 'Evento dos']);
        }
        $fake = new FakeBffCurlRequest($responses);
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), $fake));

        $this->runCommand();

        foreach (['es', 'en', 'fr', 'pt'] as $locale) {
            $this->assertSame(1, $fake->callCount("public-read/{$locale}/events/evento-1-{$locale}"));
            $this->assertSame(1, $fake->callCount("public-read/{$locale}/events/evento-2-{$locale}"));
        }
        // 34 listing/facet calls (see the count assertion above) + 4 locales x 2 featured events.
        $this->assertCount(42, $fake->requests());
    }

    public function testSeedsCollectionItemAndTechniqueDetailCachesFromBulkListingsWithoutAnyExtraCalls(): void
    {
        $responses = $this->responsesForEveryExpectedCall();
        // Every locale's "titeres" listing/detailed call returns the same
        // real piece — collection-items lean and detailed share one URL
        // (the fields differ only in the query string), so this single
        // fixture answers both and must still be enough to seed the piece's
        // own detail cache entry.
        $piezaByLocale = static fn (string $locale): array => FakeBffCurlRequest::envelope([
            ['id' => 9, 'slug' => "pieza-{$locale}", 'name' => 'Pieza real', 'summary' => 'Resumen'],
        ]);
        foreach (['es', 'en', 'fr', 'pt'] as $locale) {
            $responses["public-read/{$locale}/collection-items"] = $piezaByLocale($locale);
        }
        $responses['public/catalog/techniques'] = FakeBffCurlRequest::facet([
            ['id' => 3, 'slug' => 'tecnica-real', 'name' => 'Técnica real', 'summary' => 'Resumen técnica'],
        ]);

        $fake = new FakeBffCurlRequest($responses);
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), $fake));

        $this->runCommand();
        $totalCalls = count($fake->requests());

        // The piece's OWN detail cache entry must exist even though
        // collectionItem() was never called — seeded straight from the
        // bulk "collection-items" response, zero extra HTTP calls.
        foreach (['es', 'en', 'fr', 'pt'] as $locale) {
            $itemCacheKey = $this->cacheKeyFor("public-read/{$locale}/collection-items/pieza-{$locale}", []);
            $cached = Services::cache()->get($itemCacheKey);
            $this->assertNotNull($cached, "collectionItem({$locale}, 'pieza-{$locale}') should already be cached");
            $this->assertSame('Pieza real', $cached['data']['name'] ?? null);
        }

        $techniqueCacheKey = $this->cacheKeyFor('public/catalog/techniques/tecnica-real', []);
        $cachedTechnique = Services::cache()->get($techniqueCacheKey);
        $this->assertNotNull($cachedTechnique, "technique('tecnica-real') should already be cached");
        $this->assertSame('Técnica real', $cachedTechnique['data']['name'] ?? null);

        // Seeding must not have made a single additional HTTP call — same
        // total as the plain listing/facet run.
        $this->assertSame(34, $totalCalls);
    }

    /** @param array<string, mixed> $query */
    private function cacheKeyFor(string $path, array $query): string
    {
        $client = new BffTotemClient(Services::cache());
        $method = (new ReflectionClass($client))->getMethod('cacheKey');
        $method->setAccessible(true);

        return $method->invoke($client, $path, $query);
    }

    public function testStaysAvailableWhenTheBffIsPartiallyUnreachable(): void
    {
        $fake = new FakeBffCurlRequest(failTransport: true);
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), $fake));

        // The command itself must not throw even when every call is unavailable —
        // it logs and reports, it never lets a single unreachable screen abort the run.
        $this->runCommand();

        $this->assertGreaterThan(0, count($fake->requests()));
    }

    private function runCommand(): void
    {
        $command = new WarmBffCache(Services::logger(), Services::commands());
        $command->setDelayMicroseconds(0);
        $command->run([]);
    }

    /** @return array<string, array{status:int, body:mixed}> */
    private function responsesForEveryExpectedCall(): array
    {
        $responses = [];
        foreach (['es', 'en', 'fr', 'pt'] as $locale) {
            $responses["public-read/{$locale}/events"]                 = FakeBffCurlRequest::envelope([]);
            $responses["public-read/{$locale}/entries/teatroescuela"]  = FakeBffCurlRequest::envelope([]);
            $responses["public-read/{$locale}/collection-items"]       = FakeBffCurlRequest::envelope([]);
        }
        $responses['public/catalog/techniques']  = FakeBffCurlRequest::facet([]);
        $responses['public/catalog/categories']  = FakeBffCurlRequest::facet([]);

        return $responses;
    }
}
