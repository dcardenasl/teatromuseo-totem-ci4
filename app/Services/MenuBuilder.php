<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Builds card items used by Tótem menu grids.
 */
final class MenuBuilder
{
    /**
     * Build a menu card item.
     *
     * @return array<string, mixed>
     */
    public function item(string $title, string $href, string $copy, string $class, string $img = ''): array
    {
        return [
            'title' => $title,
            'href'  => base_url($href),
            'copy'  => $copy,
            'class' => $class,
            'img'   => $img !== '' ? 'assets/img/' . $img : '',
        ];
    }
}
