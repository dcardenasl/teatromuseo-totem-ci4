<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\SlugResolver;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Handles collection screens: main, techniques, exhibits and item details.
 */
final class CollectionController extends BaseTotemController
{
    public function collectionMain(): string
    {
        return view('totem/collection_main', array_merge(
            $this->pageMeta(lang('Collection.main_title')),
            ['nav' => $this->shellNav(base_url('museo'))]
        ));
    }

    public function collectionTechniques(): string
    {
        return view('totem/collection_techniques', array_merge(
            $this->pageMeta(lang('Collection.techniques_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
                'techniques' => $this->totemApi()->techniques(),
            ]
        ));
    }

    public function collectionPuppetsExhibit(): string
    {
        return view('totem/collection_puppets_exhibit', array_merge(
            $this->pageMeta(lang('Collection.puppets_exhibit_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
            ]
        ));
    }

    public function collectionTechnique(string $slug): string
    {
        $techniques = $this->totemApi()->techniques();
        $id         = (new SlugResolver())->resolveId($techniques, $slug);

        if ($id === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $technique = $this->totemApi()->technique($id);
        $prefix    = lang('Collection.technique_prefix');
        $prefix    = is_string($prefix) ? $prefix : '';
        $itemTitle = is_string($technique['title'] ?? null) ? (string) $technique['title'] : '';

        return view('totem/collection_technique_detail', array_merge(
            $this->pageMeta($prefix . ' - ' . $itemTitle),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion/titeres/tecnicas')),
                'technique' => $technique,
            ]
        ));
    }

    public function collectionMasksExhibit(): string
    {
        return view('totem/collection_masks_exhibit', array_merge(
            $this->pageMeta(lang('Collection.masks_exhibit_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
            ]
        ));
    }

    public function collectionMasksTraditions(): string
    {
        $traditions = [
            ['title' => lang('Collection.tradition_comedia_arte'), 'slug' => 'comedia-arte'],
            ['title' => lang('Collection.tradition_comedia_andes'), 'slug' => 'comedia-andes'],
        ];

        return view('totem/collection_masks_traditions', array_merge(
            $this->pageMeta(lang('Collection.masks_traditions_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
                'traditions' => $traditions,
            ]
        ));
    }

    public function collectionMaskTradition(string $slug): string
    {
        $titles = [
            'comedia-arte'  => lang('Collection.tradition_comedia_arte'),
            'comedia-andes' => lang('Collection.tradition_comedia_andes'),
        ];

        if (! isset($titles[$slug])) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_mask_tradition', array_merge(
            $this->pageMeta($titles[$slug]),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion/mascaras/tradiciones')),
                'tradition' => [
                    'slug'  => $slug,
                    'title' => $titles[$slug],
                ],
            ]
        ));
    }

    public function collectionItem(int $id): string
    {
        $prefix = lang('Collection.item_title');
        $prefix = is_string($prefix) ? $prefix : '';

        return view('totem/collection_item_detail', array_merge(
            $this->pageMeta($prefix . ' - ' . $id),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
                'id'  => $id,
            ]
        ));
    }
}
