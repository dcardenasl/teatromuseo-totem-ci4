<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class SchoolControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testTheaterSchoolRouteRendersCourseSection(): void
    {
        $result = $this->get('teatro-escuela');

        $result->assertStatus(200);
        $result->assertSee('Teatro escuela');
        $result->assertSee('school-course');
    }
}
