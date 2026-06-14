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
                'nav'    => $this->shellNav(),
                'months' => $context['months'],
                'events' => $context['events'],
            ]
        ));
    }

    public function billboardDetail(?string $slug = null): string
    {
        $title        = lang('Billboard.fallback_title_1');
        $copy         = lang('Billboard.detail_copy_1');
        $tags         = [lang('Billboard.fallback_audience_adults'), lang('Billboard.event_type_masks')];
        $image        = 'assets/img/billboard/la-malattia-di-nogasto-poster.webp';
        $closingImage = 'assets/img/billboard/la-malattia-di-nogasto-collage.webp';
        $qrImage      = 'assets/img/school/teatroescuela-qr.webp';
        $closingNote  = lang('Billboard.default_closing_note');

        if ($slug === 'muaki') {
            $title = lang('Billboard.fallback_title_2');
            $copy  = lang('Billboard.detail_copy_2');
            $tags  = [lang('Billboard.fallback_audience_adults'), lang('Billboard.event_type_masks')];
        } elseif ($slug === 'ayayai') {
            $title = lang('Billboard.fallback_title_3');
            $copy  = lang('Billboard.detail_copy_3');
            $tags  = [lang('Billboard.fallback_audience_family'), lang('Billboard.event_type_clowns')];
        } elseif ($slug === 'rock-festival') {
            $title = lang('Billboard.fallback_title_4');
            $copy  = lang('Billboard.detail_copy_4');
            $tags  = [lang('Billboard.fallback_audience_adults'), lang('Billboard.event_type_music')];
        }

        return view('totem/billboard_detail', array_merge(
            $this->pageMeta(lang('Menu.billboard_detail')),
            [
                'nav' => $this->shellNav(base_url('cartelera')),
                'detail' => [
                    'tags'         => $tags,
                    'title'        => $title,
                    'image'        => $image,
                    'company'      => lang('Billboard.default_company'),
                    'direction'    => lang('Billboard.default_direction'),
                    'date'         => lang('Billboard.default_date'),
                    'time'         => lang('Billboard.default_time'),
                    'duration'     => lang('Billboard.default_duration'),
                    'price'        => lang('Billboard.default_price'),
                    'copy'         => $copy,
                    'closingImage' => $closingImage,
                    'qrImage'      => $qrImage,
                    'closingNote'  => $closingNote,
                ],
            ]
        ));
    }
}
