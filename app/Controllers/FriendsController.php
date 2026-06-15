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
                'eyebrow' => lang('Menu.friends_copy'),
                'title' => lang('Menu.friends'),
                'copy' => lang('Section.friends_copy'),
                'image' => 'menu/menu_amigos.webp',
                'imageAlt' => lang('Menu.friends'),
            ]
        ));
    }

    public function extensionContact(): string
    {
        return view('totem/extension_contact', array_merge(
            $this->pageMeta(lang('Extension.title')),
            ['nav' => $this->shellNav(base_url('menu'))]
        ));
    }
}
