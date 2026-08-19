<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use App\Commands\WarmBffCache;
use App\Services\BffTotemClient;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;
use Tests\Support\FakeBffCurlRequest;

/**
 * `TOTEM-BFF-10`: guards the background cache warm-up command against two
 * regressions — calling something other than the exact listing/facet
 * methods the screens use (which would either warm the wrong keys or start
 * an unbounded per-item fan-out ADR-010 forbids), and losing its strictly
 * sequential execution order (no `curl_multi`, no concurrency primitives).
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

    public function testNeverCallsPerSlugDetailMethods(): void
    {
        $fake = new FakeBffCurlRequest($this->responsesForEveryExpectedCall());
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), $fake));

        $this->runCommand();

        foreach ($fake->requests() as $request) {
            $this->assertStringNotContainsString(
                '/events/',
                $request['url'],
                'warm-up must never call a detail endpoint — only bounded listing/facet calls',
            );
            $this->assertStringNotContainsString('/entries/teatroescuela/', $request['url']);
            $this->assertMatchesRegularExpression(
                '#^(public-read/[a-z]{2}/(events|entries/teatroescuela|collection-items)|public/catalog/(techniques|categories))$#',
                $request['url'],
            );
        }
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
