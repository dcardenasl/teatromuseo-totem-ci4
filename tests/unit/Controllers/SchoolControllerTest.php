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
final class SchoolControllerTest extends CIUnitTestCase
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

    public function testTheaterSchoolRouteRendersRealCourses(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/entries/teatroescuela' => FakeBffCurlRequest::envelope([
                [
                    'title' => 'La Escuela de los Nuevos Comediantes',
                    'blocks' => [[
                        'block_key' => 'teatroescuela_ficha',
                        'block_data' => ['activity_type' => 'course', 'summary' => 'Resumen', 'instructors' => []],
                    ]],
                ],
            ]),
        ])));

        $result = $this->get('teatro-escuela');

        $result->assertStatus(200);
        $result->assertSee('Teatro escuela');
        $result->assertSee('La Escuela de los Nuevos Comediantes');
        $result->assertSee('school-course');
        $result->assertSee('school-courses');
    }

    public function testTheaterSchoolRouteShowsAnHonestEmptyStateWhenThereAreNoCourses(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest([
            'public-read/es/entries/teatroescuela' => FakeBffCurlRequest::envelope([]),
        ])));

        $result = $this->get('teatro-escuela');

        $result->assertStatus(200);
        $result->assertSee(lang_str('Section.school_no_courses_copy'));
    }

    public function testTheaterSchoolRouteShowsAnHonestUnavailableStateWhenTheBffIsUnreachable(): void
    {
        Services::injectMock('totemApi', new BffTotemClient(Services::cache(), new FakeBffCurlRequest(failTransport: true)));

        $result = $this->get('teatro-escuela');

        $result->assertStatus(200);
        // Only the courses block degrades — the static hero/intro/stats design
        // must keep rendering even when the BFF is unreachable (regression:
        // an earlier version hid the entire screen behind "unavailable").
        $result->assertSee('school-page__hero');
        $result->assertSee('school-stats');
        $result->assertSee(lang_str('Section.school_intro'));
        $result->assertSee(lang_str('Common.content_unavailable_title'));
    }
}
