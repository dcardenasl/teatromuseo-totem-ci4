<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * Provides static fallback data for the museum today screen.
 */
final class MuseumFallbackRepository
{
    /**
     * Fallback blocks when the museum API returns no content.
     *
     * @return list<array<string, mixed>>
     */
    public function blocks(): array
    {
        return [
            [
                'index'    => '01',
                'title'    => lang('MuseumInfo.today_empty_title'),
                'copy'     => lang('MuseumInfo.today_empty_copy'),
                'fallback' => true,
            ],
        ];
    }
}
