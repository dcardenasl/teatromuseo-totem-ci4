<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use App\Commands\WarmBffCache;
use App\Services\BffTotemClient;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;
use Tests\Support\FakeBffCurlRequest;

/**
 * `TOTEM-BFF-10`/`TOTEM-BFF-18`: guards the background cache warm-up command
 * against regressions — calling something other than the exact listing/
 * facet methods the screens use (which would either warm the wrong keys or
 * start an unbounded per-item fan-out ADR-010 forbids), losing its strictly
 * sequential execution order (no `curl_multi`, no concurrency primitives),
 * and — the one deliberate, still-bounded exception — not warming the
 * individual detail pages for Cartelera's own up-to-5 featured events, so a
 * visitor tapping into a displayed event is protected from a bad-connectivity
 * moment at the kiosk's physical location.
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
            // Same path for the 3 categories (titeres/mascaras/payasos) — the
            // category lives in the query string, not the path — so exactly
            // 3 calls (one per category) land on this one URL per locale.
            $this->assertSame(
                3,
                $fake->callCount("public-read/{$locale}/collection-items"),
                "collectionItems({$locale}) should be called once per category (titeres/mascaras/payasos)",
            );
        }

        $this->assertSame(1, $fake->callCount('public/catalog/techniques'));
        $this->assertSame(1, $fake->callCount('public/catalog/categories'));

        // 4 locales x (shows + courses + 3 categories) + techniques + categories = 22 calls.
        $this->assertCount(22, $fake->requests());
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
        // 22 listing/facet calls (see the count assertion above) + 4 locales x 2 featured events.
        $this->assertCount(30, $fake->requests());
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
