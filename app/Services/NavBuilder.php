<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Builds the shell navigation actions shown in the Tótem header.
 */
final class NavBuilder
{
    /**
     * Build the header navigation.
     *
     * @return list<array<string, mixed>>
     */
    public function build(?string $backHref = null): array
    {
        $currentUri = uri_string();
        $fromParam  = '';

        if ($currentUri !== '') {
            $fromParam = '?from=' . urlencode($currentUri);
        }

        return [
            [
                'label' => lang('Nav.back'),
                'href'  => $backHref ?? base_url('menu'),
                'icon'  => 'arrow-left',
                'class' => 'pill-button pill-button--back',
            ],
            [
                'label' => lang('Nav.lang'),
                'href'  => base_url('language' . $fromParam),
                'icon'  => 'lang',
                'class' => 'pill-button pill-button--lang',
            ],
            [
                'label' => lang('Nav.home'),
                'href'  => base_url('menu'),
                'icon'  => 'home',
                'class' => 'pill-button pill-button--home',
            ],
        ];
    }
}
