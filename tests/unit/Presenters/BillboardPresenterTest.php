<?php

namespace Tests\Unit\Presenters;

use App\Presenters\BillboardPresenter;
use App\Services\TotemApiResult;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class BillboardPresenterTest extends TestCase
{
    public function testPresentListReturnsUnavailableStateWithNoEventsWhenTheSourceIsDown(): void
    {
        $presenter = new BillboardPresenter();
        $context = $presenter->presentList(TotemApiResult::unavailable(), 'es');

        self::assertSame('unavailable', $context['state']);
        self::assertSame([], $context['events']);
        self::assertSame([], $context['months']);
    }

    public function testPresentListReturnsAnHonestEmptyListWhenThereAreGenuinelyNoShows(): void
    {
        $presenter = new BillboardPresenter();
        $context = $presenter->presentList(TotemApiResult::fresh([]), 'es');

        self::assertSame('fresh', $context['state']);
        self::assertSame([], $context['events']);
    }

    public function testPresentListMapsRealEventFieldsIntoAnEventCard(): void
    {
        $presenter = new BillboardPresenter();
        $context = $presenter->presentList(TotemApiResult::fresh([
            [
                'id' => 7,
                'title' => 'Obra de prueba',
                'description' => 'Resumen',
                'event_type' => 'theatre',
                'slug' => 'obra-prueba',
                'cover_image' => ['url' => 'https://files.example/obra.webp'],
                'next_occurrence_at' => '2026-06-15 20:00:00',
            ],
        ]), 'es');

        self::assertCount(1, $context['events']);
        $event = $context['events'][0];
        self::assertSame('obra-prueba', $event['slug']);
        self::assertSame('Obra de prueba', $event['title']);
        self::assertSame('Resumen', $event['copy']);
        self::assertSame('https://files.example/obra.webp', $event['image']);
        self::assertSame('Teatro', $event['tag']);
        self::assertSame('15', $event['day']);
        self::assertNotSame([], $context['months']);
    }

    public function testPresentListFallsBackToTheEventIdWhenNoSlugIsPublished(): void
    {
        $presenter = new BillboardPresenter();
        $context = $presenter->presentList(TotemApiResult::fresh([
            ['id' => 42, 'title' => 'Sin slug', 'description' => '', 'event_type' => ''],
        ]), 'es');

        self::assertSame('42', $context['events'][0]['slug']);
    }

    public function testPresentListCapsAtFiveEventsTrustingTheBffsAgendaOrder(): void
    {
        // shows() requests sort=agenda, which the BFF already orders
        // upcoming-soonest-first then past-most-recent-first — the
        // presenter's job is only to cap, not to re-sort.
        $presenter = new BillboardPresenter();
        $show = static fn (string $title): array => ['id' => $title, 'title' => $title, 'event_type' => ''];

        $context = $presenter->presentList(TotemApiResult::fresh([
            $show('Uno'), $show('Dos'), $show('Tres'), $show('Cuatro'), $show('Cinco'), $show('Seis'),
        ]), 'es');

        self::assertCount(5, $context['events']);
        self::assertSame(['Uno', 'Dos', 'Tres', 'Cuatro', 'Cinco'], array_column($context['events'], 'title'));
    }

    public function testPresentListUsesTheLastOccurrenceDateToFillPastEvents(): void
    {
        $presenter = new BillboardPresenter();
        $context = $presenter->presentList(TotemApiResult::fresh([
            [
                'id' => 1,
                'title' => 'Función pasada',
                'event_type' => '',
                'next_occurrence_at' => null,
                'last_occurrence_at' => '2026-01-10 16:30:00',
            ],
        ]), 'es');

        $event = $context['events'][0];
        self::assertSame('10', $event['day']);
        self::assertStringContainsString('16.30', $event['timeLabel']);
    }

    public function testPresentDetailReturnsNullWhenTheSourceConfirmsNothingAtThisSlug(): void
    {
        $presenter = new BillboardPresenter();

        self::assertNull($presenter->presentDetail(TotemApiResult::fresh(null), 'es'));
    }

    public function testPresentDetailMapsRealEventAndOccurrenceFields(): void
    {
        $presenter = new BillboardPresenter();
        $detail = $presenter->presentDetail(TotemApiResult::fresh([
            'title' => 'Juan pirquinero',
            'description' => 'Copia larga',
            'event_type' => 'puppets',
            'cover_image' => ['url' => 'https://files.example/juan.webp'],
            'gallery_images' => [['url' => 'https://files.example/juan-2.webp']],
            'occurrences' => [
                ['start_time' => '2099-01-05 16:30:00', 'venue_name' => 'Sala Principal'],
            ],
        ]), 'es');

        self::assertNotNull($detail);
        self::assertSame('Juan pirquinero', $detail['title']);
        self::assertSame('Copia larga', $detail['copy']);
        self::assertSame(['Títeres'], $detail['tags']);
        self::assertSame('Sala Principal', $detail['venue']);
        self::assertSame(['https://files.example/juan.webp', 'https://files.example/juan-2.webp'], $detail['images']);
        self::assertNotSame('', $detail['date']);
        self::assertNotSame('', $detail['time']);
        self::assertSame('assets/img/school/teatroescuela-qr.webp', $detail['qrImage']);
        self::assertSame('assets/animations/billboard.webp', $detail['closingImage']);
    }
}
