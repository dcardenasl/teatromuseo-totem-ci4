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
 * `TOTEM-BFF-13`/`TOTEM-BFF-16`: fills the STALE-path gap for Colección the
 * same way as Cartelera/TeatroEscuela, and guards the fix landed for the
 * gap found while writing this — the exhibit screens now call
 * `TotemApiResult::isAvailable()` just like Cartelera/TeatroEscuela, so a
 * fully unreachable BFF with no stale copy renders `content_unavailable.php`
 * instead of silently looking identical to a genuinely empty category.
 *
 * @internal
 */
final class CollectionBffResilienceTest extends CIUnitTestCase
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

    public function testPuppetsExhibitServesRealStaleItemsWhenTheBffGoesDown(): void
    {
        $this->warmRealItemIntoCache();

        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('museo/coleccion/titeres/exhibicion');

        $result->assertStatus(200);
        $result->assertSee('Juan el titiritero (stale)');
    }

    public function testPuppetsExhibitFallsBackToTheHonestUnavailableStateOnceTheStaleCopyIsGone(): void
    {
        $this->warmRealItemIntoCache();
        $this->deleteStaleCache();

        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('museo/coleccion/titeres/exhibicion');

        $result->assertStatus(200);
        $result->assertDontSee('Juan el titiritero');
        $result->assertSee(lang_str('Common.content_unavailable_title'));
    }

    public function testPuppetsExhibitShowsTheHonestEmptyStateWhenTheCategoryIsGenuinelyEmpty(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/collection-items' => FakeBffCurlRequest::envelope([]),
        ])));

        $result = $this->get('museo/coleccion/titeres/exhibicion');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Collection.no_items_title'));
        $result->assertDontSee(lang_str('Common.content_unavailable_title'));
    }

    private function warmRealItemIntoCache(): void
    {
        $client = new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/collection-items' => FakeBffCurlRequest::envelope([
                ['id' => 9, 'name' => 'Juan el titiritero (stale)', 'slug' => 'juan-el-titiritero', 'summary' => 'Pieza real'],
            ]),
        ]));
        $client->collectionItems('es', category: 'titeres');

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

        return $method->invoke($client, 'public-read/es/collection-items', [
            'per_page' => 100,
            'category' => 'titeres',
        ]);
    }
}
