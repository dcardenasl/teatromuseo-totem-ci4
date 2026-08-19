<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Presenters\BillboardPresenter;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Handles the billboard list and detail screens.
 */
final class BillboardController extends BaseTotemController
{
    public function billboard(): string
    {
        $locale = $this->request->getLocale();
        $result = $this->totemApi()->shows($locale);
        $context = (new BillboardPresenter())->presentList($result, $locale);

        return view('totem/billboard', array_merge(
            $this->pageMeta(lang('Menu.programming')),
            [
                'nav'          => $this->shellNav(),
                'unavailable'  => $context['state'] === 'unavailable',
                'stale'        => $context['state'] === 'stale',
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
        if ($slug === null || $slug === '') {
            throw PageNotFoundException::forPageNotFound();
        }

        $locale = $this->request->getLocale();
        $result = $this->totemApi()->show($locale, $slug);
        $presenter = new BillboardPresenter();

        if ($result->state === 'unavailable') {
            return view('totem/billboard_detail', array_merge(
                $this->pageMeta(lang('Menu.billboard_detail')),
                [
                    'nav' => $this->shellNav(base_url('cartelera')),
                    'unavailable' => true,
                    'detail' => null,
                ]
            ));
        }

        $detail = $presenter->presentDetail($result, $locale);
        if ($detail === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/billboard_detail', array_merge(
            $this->pageMeta(lang('Menu.billboard_detail')),
            [
                'nav' => $this->shellNav(base_url('cartelera')),
                'unavailable' => false,
                'stale' => $result->state === 'stale',
                'detail' => $detail,
            ]
        ));
    }
}
