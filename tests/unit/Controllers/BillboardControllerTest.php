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

    public function testBillboardDetailRouteRendersTheRealShow(): void
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

        $result = $this->get('cartelera/detalle/palabras-cantadas-del-viento');

        $result->assertStatus(200);
        $result->assertSee('Palabras cantadas del viento');
    }

    public function testBillboardDetailRouteIs404ForAConfirmedNonexistentShow(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/events/no-existe' => FakeBffCurlRequest::notFound(),
        ])));

        $result = $this->get('cartelera/detalle/no-existe');

        $result->assertStatus(404);
    }
}
