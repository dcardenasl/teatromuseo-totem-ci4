<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Presenters\BillboardPresenter;

/**
 * Handles the billboard list and detail screens.
 */
final class BillboardController extends BaseTotemController
{
    public function billboard(): string
    {
        $presenter = new BillboardPresenter();
        $context   = $presenter->present(
            $this->totemApi()->shows(),
            $this->request->getLocale(),
        );

        return view('totem/billboard', array_merge(
            $this->pageMeta(lang('Menu.programming')),
            [
                'nav'          => $this->shellNav(),
                'months'       => $context['months'],
                'events'       => $context['events'],
                'titleClass'   => 'billboard-title',
                'titleWidth'   => '8.5ch',
                'footerVariant' => 'billboard',
            ]
        ));
    }

    public function billboardDetail(?string $slug = null): string
    {
        $fallback = new \App\Repositories\BillboardFallbackRepository();
        $detail   = $fallback->detail((string) $slug);

        return view('totem/billboard_detail', array_merge(
            $this->pageMeta(lang('Menu.billboard_detail')),
            [
                'nav' => $this->shellNav(base_url('cartelera')),
                'detail' => $detail,
            ]
        ));
    }
}
