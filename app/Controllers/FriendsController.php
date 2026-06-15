<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Handles friends section and extension/contact screens.
 */
final class FriendsController extends BaseTotemController
{
    public function friends(): string
    {
        return view('totem/friends', array_merge(
            $this->pageMeta(lang('Menu.friends')),
            [
                'nav' => $this->shellNav(),
                'eyebrow' => lang('Section.construction_eyebrow'),
                'title' => lang('Menu.friends'),
                'copy' => lang('Section.construction_copy'),
            ]
        ));
    }

    public function extensionContact(): string
    {
        return view('totem/friends', array_merge(
            $this->pageMeta(lang('Extension.title')),
            [
                'nav' => $this->shellNav(base_url('menu')),
                'eyebrow' => lang('Section.construction_eyebrow'),
                'title' => lang('Extension.title'),
                'copy' => lang('Section.construction_copy'),
            ]
        ));
    }
}
