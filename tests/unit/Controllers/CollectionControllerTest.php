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
final class CollectionControllerTest extends CIUnitTestCase
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

    public function testCollectionMainRouteEnablesExhibitsOnlyForCategoriesWithRealItems(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public/catalog/categories' => FakeBffCurlRequest::facet([
                ['id' => 1, 'slug' => 'payasos', 'item_count' => 3],
                ['id' => 2, 'slug' => 'mascaras', 'item_count' => 0],
            ]),
        ])));

        $result = $this->get('museo/coleccion');

        $result->assertStatus(200);
        $result->assertSee('collection-band--puppets');
        $result->assertSee('collection-band--masks');
        $result->assertSee('collection-band--clowns');
        $result->assertSee('museo/coleccion/payasos/exhibicion');
        $result->assertDontSee('museo/coleccion/mascaras/exhibicion');
    }

    public function testCollectionMainRouteShowsAnUnavailableStateWhenTheCatalogBffIsUnreachable(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('museo/coleccion');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Common.content_unavailable_title'));
    }

    public function testCollectionTechniquesRouteRendersRealTechniques(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public/catalog/techniques' => FakeBffCurlRequest::facet([
                ['id' => 1, 'name' => 'Títeres de Hilo', 'slug' => 'titere-de-hilo', 'summary' => 'Marionetas suspendidas'],
            ]),
        ])));

        $result = $this->get('museo/coleccion/titeres/tecnicas');

        $result->assertStatus(200);
        $result->assertSee('collection-grid--techniques');
        $result->assertSee('Títeres de Hilo');
    }

    public function testCollectionTechniquesRouteShowsAnHonestUnavailableStateWhenTheBffIsUnreachable(): void
    {
        // TOTEM-BFF-16: this screen used to render the same empty grid for
        // "BFF down" and "genuinely no techniques" — now it matches
        // Cartelera/TeatroEscuela and shows the honest unavailable state.
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('museo/coleccion/titeres/tecnicas');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Common.content_unavailable_title'));
    }

    public function testCollectionTechniquesRouteShowsAnHonestEmptyStateWhenThereAreNoTechniques(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public/catalog/techniques' => FakeBffCurlRequest::facet([]),
        ])));

        $result = $this->get('museo/coleccion/titeres/tecnicas');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Collection.no_items_title'));
        $result->assertDontSee(lang_str('Common.content_unavailable_title'));
    }

    public function testCollectionPuppetsExhibitRendersRealItems(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/collection-items' => FakeBffCurlRequest::envelope([
                ['id' => 9, 'name' => 'Juan el titiritero', 'slug' => 'juan-el-titiritero', 'summary' => 'Pieza real'],
            ]),
        ])));

        $result = $this->get('museo/coleccion/titeres/exhibicion');

        $result->assertStatus(200);
        $result->assertSee('Juan el titiritero');
    }

    public function testCollectionClownsExhibitShowsAnHonestUnavailableStateWhenTheBffIsUnreachable(): void
    {
        // TOTEM-BFF-16: this screen's own curated hero fallback is meant for
        // a genuinely empty category, not a BFF outage — an unreachable BFF
        // must show the honest unavailable state instead.
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('museo/coleccion/payasos/exhibicion');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Common.content_unavailable_title'));
    }

    public function testCollectionClownsExhibitShowsTheCuratedFallbackWhenGenuinelyEmpty(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/collection-items' => FakeBffCurlRequest::envelope([]),
        ])));

        $result = $this->get('museo/coleccion/payasos/exhibicion');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Collection.collection_history'));
        $result->assertDontSee(lang_str('Common.content_unavailable_title'));
    }

    public function testCollectionItemRouteRendersRealItemFields(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/collection-items/juan-el-titiritero' => FakeBffCurlRequest::envelope([
                'id' => 9,
                'name' => 'Juan el titiritero',
                'slug' => 'juan-el-titiritero',
                'summary' => 'Pieza real',
                'inventory_code' => 'TM-0042',
                'category' => ['slug' => 'titeres'],
                'techniques' => [],
            ]),
            'public-read/es/collection-items' => FakeBffCurlRequest::envelope([]),
        ])));

        $result = $this->get('museo/coleccion/fichas/juan-el-titiritero');

        $result->assertStatus(200);
        $result->assertSee('Juan el titiritero');
        $result->assertSee('TM-0042');
    }

    public function testCollectionItemRouteIs404ForAConfirmedNonexistentPiece(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/collection-items/no-existe' => FakeBffCurlRequest::notFound(),
        ])));

        $result = $this->get('museo/coleccion/fichas/no-existe');

        $result->assertStatus(404);
    }

    public function testCollectionMasksTraditionsRouteRendersList(): void
    {
        $result = $this->get('museo/coleccion/mascaras/tradiciones');

        $result->assertStatus(200);
        $result->assertSee('Tradiciones de Máscaras');
        $result->assertDontSee('content-panel');
    }

    public function testObsoleteClownsRouteReturnsRedirect(): void
    {
        $result = $this->get('museo/coleccion/payasos');

        $result->assertRedirect();
    }
}
