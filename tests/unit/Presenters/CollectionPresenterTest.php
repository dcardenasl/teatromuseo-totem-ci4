<?php

namespace Tests\Unit\Presenters;

use App\Presenters\CollectionPresenter;
use App\Services\TotemApiResult;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class CollectionPresenterTest extends TestCase
{
    public function testExhibitCardsMapsRealItemFields(): void
    {
        $presenter = new CollectionPresenter();
        $cards = $presenter->exhibitCards(TotemApiResult::fresh([
            ['id' => 1, 'name' => 'Pieza uno', 'slug' => 'pieza-uno', 'summary' => 'Resumen', 'cover_image' => ['url' => 'https://files.example/uno.webp']],
        ]));

        self::assertCount(1, $cards);
        self::assertSame('Pieza uno', $cards[0]['title']);
        self::assertSame('museo/coleccion/fichas/pieza-uno', $cards[0]['href']);
        self::assertSame('https://files.example/uno.webp', $cards[0]['image']);
    }

    public function testExhibitCardsReturnsEmptyListWhenTheSourceHasNothing(): void
    {
        $presenter = new CollectionPresenter();

        self::assertSame([], $presenter->exhibitCards(TotemApiResult::fresh([])));
        self::assertSame([], $presenter->exhibitCards(TotemApiResult::unavailable()));
    }

    public function testItemDetailReturnsNullForAConfirmedNonexistentItem(): void
    {
        $presenter = new CollectionPresenter();

        self::assertNull($presenter->itemDetail(TotemApiResult::fresh(null), TotemApiResult::fresh([])));
    }

    public function testItemDetailMapsRealFieldsIncludingTheInventoryCode(): void
    {
        $presenter = new CollectionPresenter();
        $detail = $presenter->itemDetail(
            TotemApiResult::fresh([
                'id' => 9,
                'name' => 'Juan el titiritero',
                'slug' => 'juan-el-titiritero',
                'summary' => 'Resumen corto',
                'contenido' => 'Descripción larga',
                'inventory_code' => 'TM-0042',
                'origin' => 'Chile',
                'dimensions' => '30x20 cm',
                'period' => '1990',
                'donated_by' => 'Familia Pérez',
                'category' => ['slug' => 'titeres'],
                'techniques' => [['name' => 'Guante', 'slug' => 'titere-de-guante']],
                'cover_image' => ['url' => 'https://files.example/juan.webp'],
            ]),
            TotemApiResult::fresh([
                ['id' => 9, 'name' => 'Juan el titiritero', 'slug' => 'juan-el-titiritero'],
                ['id' => 10, 'name' => 'Otra pieza', 'slug' => 'otra-pieza'],
            ]),
        );

        self::assertNotNull($detail);
        self::assertSame('Juan el titiritero', $detail['title']);
        self::assertSame('Descripción larga', $detail['description']);
        self::assertSame('TM-0042', $detail['code']);
        self::assertSame('Guante', $detail['technique']);
        self::assertSame('museo/coleccion/titeres/tecnicas/titere-de-guante', $detail['techniqueHref']);
        self::assertSame('https://files.example/juan.webp', $detail['image']);
        // The current item must not appear among its own related pieces.
        self::assertCount(1, $detail['related']);
        self::assertSame('Otra pieza', $detail['related'][0]['label']);
    }

    public function testItemDetailUsesAnHonestPlaceholderForMissingFactsInsteadOfInventingThem(): void
    {
        $presenter = new CollectionPresenter();
        $detail = $presenter->itemDetail(
            TotemApiResult::fresh(['id' => 1, 'name' => 'Pieza sin metadata', 'category' => ['slug' => 'titeres'], 'techniques' => []]),
            TotemApiResult::fresh([]),
        );

        self::assertNotNull($detail);
        self::assertSame('—', $detail['origin']);
        self::assertSame('—', $detail['measurements']);
        self::assertSame('—', $detail['year']);
        self::assertSame('—', $detail['donatedBy']);
        self::assertSame('—', $detail['code']);
        self::assertSame('—', $detail['technique']);
    }

    public function testCategoryAvailabilityReflectsRealPublishedCounts(): void
    {
        $presenter = new CollectionPresenter();
        $availability = $presenter->categoryAvailability(TotemApiResult::fresh([
            ['slug' => 'payasos', 'item_count' => 2],
            ['slug' => 'mascaras', 'item_count' => 0],
        ]));

        self::assertTrue($availability['hasClowns']);
        self::assertFalse($availability['hasMasks']);
    }
}
