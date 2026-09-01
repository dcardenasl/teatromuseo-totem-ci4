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
 * `TOTEM-BFF-13`: fills the one gap `SchoolControllerTest` never covered —
 * the STALE path at the HTTP/rendered-HTML level, confirmed alongside the
 * TOTEM-BFF-03 regression rule it must never break again: only
 * `.school-courses` may degrade, the static hero/intro/stats design always
 * renders regardless of BFF state.
 *
 * @internal
 */
final class SchoolBffResilienceTest extends CIUnitTestCase
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

    public function testTheaterSchoolServesRealStaleCoursesAndKeepsTheStaticDesignVisibleWhenTheBffGoesDown(): void
    {
        $this->warmRealCourseIntoCache();

        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('teatro-escuela');

        $result->assertStatus(200);
        $result->assertSee('La Escuela de los Nuevos Comediantes (stale)');
        $result->assertSee(lang_str('Common.content_stale_note'));
        // The TOTEM-BFF-03 regression rule: static design never gates on state.
        $result->assertSee('school-page__hero');
        $result->assertSee('school-stats');
        $result->assertDontSee(lang_str('Common.content_unavailable_title'));
    }

    public function testTheaterSchoolFallsBackToTheHonestUnavailableStateOnlyInTheCoursesBlock(): void
    {
        $this->warmRealCourseIntoCache();
        $this->deleteStaleCache();

        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('teatro-escuela');

        $result->assertStatus(200);
        $result->assertSee('school-page__hero');
        $result->assertSee('school-stats');
        $result->assertSee(lang_str('Common.content_unavailable_title'));
        $result->assertDontSee('La Escuela de los Nuevos Comediantes');
    }

    private function warmRealCourseIntoCache(): void
    {
        $client = new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/entries/teatroescuela' => FakeBffCurlRequest::envelope([
                [
                    'title' => 'La Escuela de los Nuevos Comediantes (stale)',
                    'blocks' => [[
                        'block_key' => 'teatroescuela_ficha',
                        'block_data' => ['activity_type' => 'course', 'summary' => 'Resumen', 'instructors' => []],
                    ]],
                ],
            ]),
        ]));
        $client->courses('es');

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

        return $method->invoke($client, 'public-read/es/entries/teatroescuela', [
            'order_by' => 'field:start_date',
            'order_direction' => 'upcoming',
            'per_page' => 100,
        ]);
    }
}
