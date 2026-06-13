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
        return view('totem/section', array_merge(
            $this->pageMeta(lang('Menu.friends')),
            [
                'nav' => $this->shellNav(),
                'section' => [
                    'eyebrow'      => lang('Menu.friends_copy'),
                    'title'        => lang('Menu.friends'),
                    'copy'         => lang('Section.friends_copy'),
                    'visualClass'  => 'section-hero__visual section-hero__visual--friends',
                    'detailsTitle' => lang('Section.friends_details_title'),
                    'detailsCopy'  => lang('Section.friends_details_copy'),
                    'stats'        => [
                        ['label' => lang('Section.friends_stat_support_label'), 'value' => lang('Section.friends_stat_support')],
                        ['label' => lang('Section.friends_stat_network_label'), 'value' => lang('Section.friends_stat_network')],
                        ['label' => lang('Section.friends_stat_contact_label'), 'value' => lang('Section.friends_stat_contact')],
                    ],
                ],
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
