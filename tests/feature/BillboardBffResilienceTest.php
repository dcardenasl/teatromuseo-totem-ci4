<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\BffTotemClient;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use ReflectionClass;
use Tests\Support\FakeBffCurlRequest;

/**
 * `TOTEM-BFF-13`: the real gap the unit-level controller tests never
 * exercised — the STALE path, at the HTTP/rendered-HTML level. Unit tests
 * already cover fresh/honest-empty/confirmed-404/fully-unreachable for
 * Cartelera; none of them ever populated a real cache entry and then made
 * the BFF fail on the *next* request to confirm the kiosk actually serves
 * the last real content instead of an error — which is the literal promise
 * this app makes to a visitor during an outage.
 *
 * @internal
 */
final class BillboardBffResilienceTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        Services::cache()->clean();
    }

    protected function tearDown(): void
    {
        Services::reset(true);
        Services::cache()->clean();
        parent::tearDown();
    }

    public function testBillboardServesRealStaleContentInsteadOfAnErrorWhenTheBffGoesDown(): void
    {
        $this->warmRealShowIntoCache();

        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('cartelera');

        $result->assertStatus(200);
        // Real content from the outage-time cache entry — not invented, not an error page.
        $result->assertSee('Palabras cantadas del viento (stale)');
        $result->assertSee(lang_str('Common.content_stale_note'));
        $result->assertDontSee(lang_str('Common.content_unavailable_title'));
    }

    public function testBillboardFallsBackToTheHonestUnavailableStateOnceTheStaleCopyIsAlsoGone(): void
    {
        $this->warmRealShowIntoCache();
        $this->deleteStaleCache();

        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('cartelera');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Common.content_unavailable_title'));
        // Never invented content standing in for the real thing.
        $result->assertDontSee('Palabras cantadas del viento');
    }

    private function warmRealShowIntoCache(): void
    {
        $client = new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events' => FakeBffCurlRequest::envelope([
                [
                    'id' => 9,
                    'title' => 'Palabras cantadas del viento (stale)',
                    'description' => 'Teatro de sombras',
                    'event_type' => 'theatre',
                    'slug' => 'palabras-cantadas-del-viento',
                    'cover_image' => null,
                    'next_occurrence_at' => '2099-06-15 20:00:00',
                ],
            ]),
        ]));
        $client->shows('es');

        $this->deleteFreshCache($client);
    }

    private function deleteFreshCache(BffTotemClient $client): void
    {
        Services::cache()->delete($this->cacheKey($client));
    }

    private function deleteStaleCache(): void
    {
        $client = new BffTotemClient(Services::cache());
        Services::cache()->delete($this->cacheKey($client) . '_stale');
    }

    private function cacheKey(BffTotemClient $client): string
    {
        $method = (new ReflectionClass($client))->getMethod('cacheKey');
        $method->setAccessible(true);

        return $method->invoke($client, 'public-read/es/events', [
            'sort' => 'agenda',
            'per_page' => 100,
            'fields' => 'id,title,localized,cover_image,slug,next_occurrence_at,last_occurrence_at',
        ]);
    }
}
