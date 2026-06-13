<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * Provides static fallback data for the billboard screen.
 */
final class BillboardFallbackRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function months(): array
    {
        return [
            ['title' => lang('Billboard.month_may'),  'days' => ['10', '17', '24', '30']],
            ['title' => lang('Billboard.month_june'), 'days' => ['2', '9', '16', '23']],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function events(): array
    {
        return [
            [
                'tag'   => lang('Billboard.fallback_audience_family'),
                'type'  => lang('Billboard.event_type_puppets'),
                'title' => lang('Billboard.fallback_title_1'),
                'copy'  => lang('Billboard.fallback_copy_1'),
                'class' => 'event-card--family',
                'slug'  => 'la-malattia-di-nogasto',
            ],
            [
                'tag'   => lang('Billboard.fallback_audience_adults'),
                'type'  => lang('Billboard.event_type_masks'),
                'title' => lang('Billboard.fallback_title_2'),
                'copy'  => lang('Billboard.fallback_copy_2'),
                'class' => 'event-card--adult',
                'slug'  => 'muaki',
            ],
            [
                'tag'   => lang('Billboard.fallback_audience_family'),
                'type'  => lang('Billboard.event_type_clowns'),
                'title' => lang('Billboard.fallback_title_3'),
                'copy'  => lang('Billboard.fallback_copy_3'),
                'class' => 'event-card--family',
                'slug'  => 'ayayai',
            ],
            [
                'tag'   => lang('Billboard.fallback_audience_adults'),
                'type'  => lang('Billboard.event_type_music'),
                'title' => lang('Billboard.fallback_title_4'),
                'copy'  => lang('Billboard.fallback_copy_4'),
                'class' => 'event-card--music',
                'slug'  => 'rock-festival',
            ],
        ];
    }
}
