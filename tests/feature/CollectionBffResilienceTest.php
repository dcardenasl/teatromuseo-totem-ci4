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
 * `TOTEM-BFF-13`: fills the STALE-path gap for Colección the same way as
 * Cartelera/TeatroEscuela. Also documents, as a passing regression guard
 * rather than a silent gap, the real behavior found while writing this:
 * unlike Cartelera/TeatroEscuela/`collectionMain()`, the exhibit screens
 * never call `TotemApiResult::isAvailable()` — a fully unreachable BFF with
 * no stale copy renders the same empty grid as a genuinely empty category,
 * not `content_unavailable.php`. Tracked as a design decision to make in
 * `TOTEM-BFF-16` (TASKS.md) — this test asserts what the code does today so
 * a future fix is a deliberate, visible change to this test, not a silent
 * behavior shift.
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

    public function testPuppetsExhibitRendersAnEmptyGridRatherThanAnExplicitUnavailableStateOnceTheStaleCopyIsGone(): void
    {
        $this->warmRealItemIntoCache();
        $this->deleteStaleCache();

        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('museo/coleccion/titeres/exhibicion');

        $result->assertStatus(200);
        $result->assertDontSee('Juan el titiritero');
        // Documents today's real behavior (see class docblock / TOTEM-BFF-16):
        // no distinct "unavailable" message here, unlike Cartelera/TeatroEscuela.
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
