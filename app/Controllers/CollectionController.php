<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Presenters\CollectionPresenter;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Handles collection screens: main, techniques, exhibits and item details.
 *
 * All real data comes from the BFF's catalog public-read endpoints
 * (`App\Services\BffTotemClient`). There is no mock JSON and no fallback
 * repository here anymore — a category with nothing published shows an
 * honest empty state via `totem/partials/collection_grid`'s empty markup.
 */
final class CollectionController extends BaseTotemController
{
    public function collectionMain(): string
    {
        $categories = $this->totemApi()->catalogCategories(withCounts: true);
        $availability = (new CollectionPresenter())->categoryAvailability($categories);
        $hasClowns = $availability['hasClowns'];
        $hasMasks = $availability['hasMasks'];
        $catalogUnavailable = ! $categories->isAvailable();

        $sections = [
            [
                'title'     => lang('Collection.puppets'),
                'image'     => 'assets/img/museo/coleccion/titeres/titere.webp',
                'routeA'    => [
                    'label' => lang('Collection.collection_exhibit'),
                    'href'  => 'museo/coleccion/titeres/exhibicion',
                ],
                'routeB'    => [
                    'label' => lang('Collection.collection_techniques'),
                    'href'  => 'museo/coleccion/titeres/tecnicas',
                ],
                'bandClass' => 'collection-band--puppets',
            ],
            [
                'title'     => lang('Collection.clowns'),
                'image'     => 'assets/img/museo/coleccion/payasos/payaso.webp',
                'routeA'    => [
                    'label'    => lang('Collection.collection_exhibit'),
                    'href'     => $hasClowns ? 'museo/coleccion/payasos/exhibicion' : null,
                    'disabled' => !$hasClowns,
                ],
                'routeB'    => [
                    'label' => lang('Collection.collection_history'),
                    'href'  => 'museo/historia?from=museo/coleccion',
                ],
                'bandClass' => 'collection-band--clowns',
            ],
            [
                'title'     => lang('Collection.masks'),
                'image'     => 'assets/img/museo/coleccion/mascaras/mascara.webp',
                'routeA'    => [
                    'label'    => lang('Collection.collection_exhibit'),
                    'href'     => $hasMasks ? 'museo/coleccion/mascaras/exhibicion' : null,
                    'disabled' => !$hasMasks,
                ],
                'routeB'    => [
                    'label' => lang('Collection.collection_traditions'),
                    'href'  => 'museo/coleccion/mascaras/tradiciones',
                ],
                'bandClass' => 'collection-band--masks',
            ],
        ];

        return view('totem/collection_main', array_merge(
            $this->pageMeta(lang('Collection.main_title')),
            [
                'nav'      => $this->shellNav(base_url('museo')),
                'sections' => $sections,
                'catalogUnavailable' => $catalogUnavailable,
            ]
        ));
    }

    public function collectionTechniques(): string
    {
        $result = $this->totemApi()->techniques();

        return view('totem/collection_techniques', array_merge(
            $this->pageMeta(lang('Collection.techniques_title')),
            [
                'nav'        => $this->shellNav(base_url('museo/coleccion')),
                'tabs'       => $this->collectionTabs('techniques'),
                'techniques' => (new CollectionPresenter())->techniqueCards($result),
            ]
        ));
    }

    public function collectionPuppetsExhibit(): string
    {
        $locale = $this->request->getLocale();
        $result = $this->totemApi()->collectionItems($locale, category: 'titeres');

        return view('totem/collection_puppets_exhibit', array_merge(
            $this->pageMeta(lang('Collection.puppets_exhibit_title')),
            [
                'nav'   => $this->shellNav(base_url('museo/coleccion')),
                'tabs'  => $this->collectionTabs('exhibit'),
                'items' => (new CollectionPresenter())->exhibitCards($result),
            ]
        ));
    }

    public function collectionTechnique(string $slug): string
    {
        $technique = $this->totemApi()->technique($slug);

        if ($technique->state === 'unavailable') {
            return view('totem/collection_technique_detail', array_merge(
                $this->pageMeta(lang('Collection.techniques_title')),
                [
                    'nav' => $this->shellNav(base_url('museo/coleccion/titeres/tecnicas')),
                    'title' => '',
                    'unavailable' => true,
                    'technique' => null,
                ]
            ));
        }

        $relatedItems = $this->totemApi()->collectionItems($this->request->getLocale(), technique: $slug, perPage: 3);
        $presented = (new CollectionPresenter())->techniqueDetail($technique, $relatedItems);

        if ($presented === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_technique_detail', array_merge(
            $this->pageMeta($presented['pageTitle']),
            [
                'nav'       => $this->shellNav(base_url('museo/coleccion/titeres/tecnicas')),
                'title'     => '',
                'unavailable' => false,
                'technique' => $presented,
            ]
        ));
    }

    public function collectionMasksExhibit(): string
    {
        $locale = $this->request->getLocale();
        $result = $this->totemApi()->collectionItems($locale, category: 'mascaras');
        $items = (new CollectionPresenter())->exhibitCards($result);

        $tabs = [
            [
                'label'  => lang('Collection.collection_exhibit'),
                'href'   => 'museo/coleccion/mascaras/exhibicion',
                'active' => true,
            ],
            [
                'label'  => lang('Collection.collection_traditions'),
                'href'   => 'museo/coleccion/mascaras/tradiciones',
                'active' => false,
            ],
        ];

        return view('totem/collection_masks_exhibit', array_merge(
            $this->pageMeta(lang('Collection.masks_exhibit_title')),
            [
                'nav'   => $this->shellNav(base_url('museo/coleccion')),
                'tabs'  => $tabs,
                'items' => $items,
            ]
        ));
    }

    public function collectionClownsExhibit(): string
    {
        $locale = $this->request->getLocale();
        $result = $this->totemApi()->collectionItems($locale, category: 'payasos');
        $items = (new CollectionPresenter())->exhibitCards($result);

        $tabs = [
            [
                'label'  => lang('Collection.collection_exhibit'),
                'href'   => 'museo/coleccion/payasos/exhibicion',
                'active' => true,
            ],
            [
                'label'  => lang('Collection.collection_history'),
                'href'   => 'museo/historia?from=museo/coleccion',
                'active' => false,
            ],
        ];

        return view('totem/collection_clowns_exhibit', array_merge(
            $this->pageMeta(lang('Collection.clowns_title')),
            [
                'nav'   => $this->shellNav(base_url('museo/coleccion')),
                'tabs'  => $tabs,
                'items' => $items,
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
                'nav'        => $this->shellNav(base_url('museo/coleccion')),
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

        $key = str_replace('-', '_', $slug);

        $story = [
            'eyebrow'  => lang("Collection.tradition_{$key}_eyebrow"),
            'title'    => $titles[$slug],
            'image'    => "assets/img/museo/coleccion/mascaras/{$slug}.webp",
            'imageAlt' => lang("Collection.tradition_{$key}_image_alt"),
            'intro'    => lang("Collection.tradition_{$key}_intro"),
            'sections' => [
                [
                    'title' => lang("Collection.tradition_{$key}_section_title_1"),
                    'copy'  => lang("Collection.tradition_{$key}_section_copy_1"),
                ],
                [
                    'title' => lang("Collection.tradition_{$key}_section_title_2"),
                    'copy'  => lang("Collection.tradition_{$key}_section_copy_2"),
                ],
            ],
        ];

        return view('totem/collection_mask_tradition', array_merge(
            $this->pageMeta($titles[$slug]),
            [
                'nav'   => $this->shellNav(base_url('museo/coleccion/mascaras/tradiciones')),
                'story' => $story,
            ]
        ));
    }

    public function collectionItem(string $slug): string
    {
        $locale = $this->request->getLocale();
        $result = $this->totemApi()->collectionItem($locale, $slug);
        $presenter = new CollectionPresenter();

        if ($result->state === 'unavailable') {
            return view('totem/collection_item_detail', array_merge(
                $this->pageMeta(lang('Collection.item_detail_title')),
                [
                    'nav' => $this->shellNav(base_url('museo/coleccion')),
                    'title' => lang('Collection.puppets'),
                    'unavailable' => true,
                    'item' => null,
                ]
            ));
        }

        if ($result->data === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $item = $result->map();
        $categorySlug = is_string($item['category']['slug'] ?? null) ? $item['category']['slug'] : 'titeres';
        $relatedItems = $this->totemApi()->collectionItems($locale, category: $categorySlug, perPage: 4);

        $presented = $presenter->itemDetail($result, $relatedItems);
        if ($presented === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $title = match ($presented['categorySlug']) {
            'mascaras' => lang('Collection.masks'),
            'payasos'  => lang('Collection.clowns'),
            default    => lang('Collection.puppets'),
        };

        return view('totem/collection_item_detail', array_merge(
            $this->pageMeta($presented['pageTitle']),
            [
                'nav'   => $this->shellNav(base_url("museo/coleccion/{$presented['categorySlug']}/exhibicion")),
                'title' => $title,
                'unavailable' => false,
                'item'  => $presented,
            ]
        ));
    }

    // -------------------------------------------------------------------------
    // Tabs
    // -------------------------------------------------------------------------

    /**
     * @return list<array{label:string, href:string, active?:bool, disabled?:bool}>
     */
    private function collectionTabs(string $active): array
    {
        return [
            [
                'label'  => lang_str('Collection.collection_exhibit'),
                'href'   => 'museo/coleccion/titeres/exhibicion',
                'active' => $active === 'exhibit',
            ],
            [
                'label'  => lang_str('Collection.collection_techniques'),
                'href'   => 'museo/coleccion/titeres/tecnicas',
                'active' => $active === 'techniques',
            ],
        ];
    }
}
