<?php

namespace Tests\Unit\Controllers;

use App\Services\BffTotemClient;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\FakeBffCurlRequest;

/**
 * @internal
 */
final class BillboardControllerTest extends CIUnitTestCase
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

    public function testBillboardRouteRendersRealEvents(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events' => FakeBffCurlRequest::envelope([
                [
                    'id' => 1,
                    'title' => 'Obra de prueba',
                    'description' => 'Resumen',
                    'event_type' => 'theatre',
                    'slug' => 'obra-prueba',
                    'cover_image' => null,
                    'next_occurrence_at' => '2099-06-15 20:00:00',
                ],
            ]),
        ])));

        $result = $this->get('cartelera');

        $result->assertStatus(200);
        $result->assertSee('Cartelera');
        $result->assertSee('Obra de prueba');
    }

    public function testBillboardRouteShowsAnHonestEmptyStateWhenThereAreNoShows(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events' => FakeBffCurlRequest::envelope([]),
        ])));

        $result = $this->get('cartelera');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Billboard.no_shows_title'));
    }

    public function testBillboardRouteShowsAnHonestUnavailableStateWhenTheBffIsUnreachable(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('cartelera');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Common.content_unavailable_title'));
    }

    public function testBillboardDetailRouteRendersTheRealShowSynchronouslyWhenAlreadyCached(): void
    {
        // TOTEM-BFF-17: a detail page only renders synchronously when the
        // cache already has an answer (fresh or stale) — simulate that by
        // warming it via a direct client call before hitting the route.
        $client = new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events/palabras-cantadas-del-viento' => FakeBffCurlRequest::envelope([
                'title' => 'Palabras cantadas del viento',
                'description' => 'Teatro de sombras',
                'event_type' => 'theatre',
                'cover_image' => null,
                'gallery_images' => [],
                'occurrences' => [],
            ]),
        ]));
        $client->show('es', 'palabras-cantadas-del-viento');
        Services::injectMock('totemApi', $client);

        $result = $this->get('cartelera/detalle/palabras-cantadas-del-viento');

        $result->assertStatus(200);
        $result->assertSee('Palabras cantadas del viento');
        $result->assertDontSee(lang_str('Common.content_loading_label'));
    }

    public function testBillboardDetailRouteDefersToAnAsyncFetchOnAColdCache(): void
    {
        // TOTEM-BFF-17: per-slug detail pages are deliberately excluded
        // from the background warm-up (unbounded fan-out), so a slug's
        // first-ever view is always a cold cache — the shell must render
        // immediately with a loading placeholder instead of blocking on the
        // network, and point the browser at the async data endpoint.
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events/palabras-cantadas-del-viento' => FakeBffCurlRequest::envelope([
                'title' => 'Palabras cantadas del viento',
            ]),
        ])));

        $result = $this->get('cartelera/detalle/palabras-cantadas-del-viento');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Common.content_loading_label'));
        $result->assertSee('data-async-detail-url');
        $result->assertSee('cartelera/detalle/palabras-cantadas-del-viento/data');
        $result->assertDontSee('Palabras cantadas del viento');
    }

    public function testBillboardDetailDataRouteReturnsTheRealShowAsJson(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events/palabras-cantadas-del-viento' => FakeBffCurlRequest::envelope([
                'title' => 'Palabras cantadas del viento',
                'description' => 'Teatro de sombras',
                'event_type' => 'theatre',
                'cover_image' => null,
                'gallery_images' => [],
                'occurrences' => [],
            ]),
        ])));

        $result = $this->get('cartelera/detalle/palabras-cantadas-del-viento/data');

        $result->assertStatus(200);
        $result->assertJSONFragment(['state' => 'fresh']);
        $body = json_decode((string) $result->response()->getBody(), true);
        $this->assertStringContainsString('Palabras cantadas del viento', $body['html']);
    }

    public function testBillboardDetailDataRouteReportsNotFoundForAConfirmedNonexistentShow(): void
    {
        // A confirmed-404 can no longer become a real HTTP 404 here — the
        // page shell already responded 200 before this async call — so the
        // browser gets a typed state to render the "not found" copy inline.
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events/no-existe' => FakeBffCurlRequest::notFound(),
        ])));

        $result = $this->get('cartelera/detalle/no-existe/data');

        $result->assertStatus(200);
        $result->assertJSONFragment(['state' => 'not_found']);
    }

    public function testBillboardDetailDataRouteReportsUnavailableWhenTheBffIsUnreachable(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('cartelera/detalle/palabras-cantadas-del-viento/data');

        $result->assertStatus(200);
        $result->assertJSONFragment(['state' => 'unavailable']);
    }
}
