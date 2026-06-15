<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Handles collection screens: main, techniques, exhibits and item details.
 */
final class CollectionController extends BaseTotemController
{
    public function collectionMain(): string
    {
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
                    'href'     => null,
                    'disabled' => true,
                ],
                'routeB'    => [
                    'label' => lang('Collection.collection_history'),
                    'href'  => 'museo/historia',
                ],
                'bandClass' => 'collection-band--clowns',
            ],
            [
                'title'     => lang('Collection.masks'),
                'image'     => 'assets/img/museo/coleccion/mascaras/mascara.webp',
                'routeA'    => [
                    'label' => lang('Collection.collection_exhibit'),
                    'href'  => 'museo/coleccion/mascaras/exhibicion',
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
            ]
        ));
    }

    public function collectionTechniques(): string
    {
        return view('totem/collection_techniques', array_merge(
            $this->pageMeta(lang('Collection.techniques_title')),
            [
                'nav'       => $this->shellNav(base_url('museo/coleccion')),
                'tabs'      => $this->collectionTabs('techniques'),
                'techniques' => $this->collectionTechniqueCards(),
            ]
        ));
    }

    public function collectionPuppetsExhibit(): string
    {
        return view('totem/collection_puppets_exhibit', array_merge(
            $this->pageMeta(lang('Collection.puppets_exhibit_title')),
            [
                'nav'    => $this->shellNav(base_url('museo/coleccion')),
                'tabs'   => $this->collectionTabs('exhibit'),
                'items'  => $this->collectionExhibitCards(),
            ]
        ));
    }

    public function collectionTechnique(string $slug): string
    {
        $technique = $this->collectionTechniqueBySlug($slug);

        if ($technique === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_technique_detail', array_merge(
            $this->pageMeta($technique['pageTitle']),
            [
                'nav'       => $this->shellNav(base_url('museo/coleccion/titeres/tecnicas')),
                'tabs'      => $this->collectionTabs('techniques'),
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
        $item = $this->collectionItemById($id);

        return view('totem/collection_item_detail', array_merge(
            $this->pageMeta($item['pageTitle']),
            [
                'nav'  => $this->shellNav(base_url('museo/coleccion/titeres/exhibicion')),
                'tabs' => $this->collectionTabs('exhibit'),
                'item' => $item,
            ]
        ));
    }

    /**
     * @return list<array{label:string, href:string, active?:bool, disabled?:bool}>
     */
    private function collectionTabs(string $active): array
    {
        return [
            [
                'label'   => lang('Collection.collection_exhibit'),
                'href'    => 'museo/coleccion/titeres/exhibicion',
                'active'  => $active === 'exhibit',
            ],
            [
                'label'   => lang('Collection.collection_techniques'),
                'href'    => 'museo/coleccion/titeres/tecnicas',
                'active'  => $active === 'techniques',
            ],
            [
                'label'   => lang('Collection.collection_index'),
                'href'    => 'museo/coleccion',
                'active'  => $active === 'collection',
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function collectionExhibitCards(): array
    {
        $cards = [];
        $tones = ['coral', 'sky', 'violet', 'wine'];
        $count = 9;

        for ($i = 1; $i <= $count; $i++) {
            $cards[] = [
                'title' => lang('Collection.mock_puppet_title'),
                'copy'  => lang('Collection.mock_puppet_copy'),
                'href'  => 'museo/coleccion/fichas/' . $i,
                'image' => 'assets/img/museo/coleccion/titeres/titere.webp',
                'tone'  => $tones[($i - 1) % count($tones)],
            ];
        }

        return $cards;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function collectionTechniqueCards(): array
    {
        $families = [
            [
                'slug'  => 'titere-de-hilo',
                'title' => lang('Collection.technique_hilo_title'),
                'copy'  => lang('Collection.technique_card_copy'),
            ],
            [
                'slug'  => 'titere-de-guante',
                'title' => lang('Collection.technique_guante_title'),
                'copy'  => lang('Collection.technique_card_copy'),
            ],
            [
                'slug'  => 'manipulacion-directa',
                'title' => lang('Collection.technique_direct_title'),
                'copy'  => lang('Collection.technique_card_copy'),
            ],
        ];

        $tones = ['coral', 'sky', 'violet', 'wine'];
        $cards = [];

        for ($i = 0; $i < 12; $i++) {
            $family = $families[$i % 3];

            $cards[] = [
                'title' => $family['title'],
                'copy'  => $family['copy'],
                'href'  => 'museo/coleccion/titeres/tecnicas/' . $family['slug'],
                'image' => 'assets/img/museo/coleccion/titeres/titere.webp',
                'tone'  => $tones[$i % count($tones)],
            ];
        }

        return $cards;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function collectionTechniqueBySlug(string $slug): ?array
    {
        $techniques = [
            'titere-de-hilo' => [
                'pageTitle'   => lang('Collection.technique_hilo_title'),
                'title'       => lang('Collection.technique_hilo_title'),
                'subtitle'    => lang('Collection.technique_hilo_subtitle'),
                'description' => lang('Collection.technique_hilo_description'),
                'image'       => 'assets/img/museo/coleccion/titeres/titere.webp',
                'related'     => [
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/1'],
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/4'],
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/7'],
                ],
                'ctaLabel'    => lang('Collection.collection_exhibit'),
                'ctaHref'     => 'museo/coleccion/titeres/exhibicion',
            ],
            'titere-de-guante' => [
                'pageTitle'   => lang('Collection.technique_guante_title'),
                'title'       => lang('Collection.technique_guante_title'),
                'subtitle'    => lang('Collection.technique_guante_subtitle'),
                'description' => lang('Collection.technique_guante_description'),
                'image'       => 'assets/img/museo/coleccion/titeres/titere.webp',
                'related'     => [
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/2'],
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/5'],
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/8'],
                ],
                'ctaLabel'    => lang('Collection.collection_exhibit'),
                'ctaHref'     => 'museo/coleccion/titeres/exhibicion',
            ],
            'manipulacion-directa' => [
                'pageTitle'   => lang('Collection.technique_direct_title'),
                'title'       => lang('Collection.technique_direct_title'),
                'subtitle'    => lang('Collection.technique_direct_subtitle'),
                'description' => lang('Collection.technique_direct_description'),
                'image'       => 'assets/img/museo/coleccion/titeres/titere.webp',
                'related'     => [
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/3'],
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/6'],
                    ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/9'],
                ],
                'ctaLabel'    => lang('Collection.collection_exhibit'),
                'ctaHref'     => 'museo/coleccion/titeres/exhibicion',
            ],
        ];

        return $techniques[$slug] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    private function collectionItemById(int $id): array
    {
        $families = [
            [
                'technique'     => lang('Collection.technique_hilo_title'),
                'techniqueHref'  => 'museo/coleccion/titeres/tecnicas/titere-de-hilo',
            ],
            [
                'technique'     => lang('Collection.technique_guante_title'),
                'techniqueHref'  => 'museo/coleccion/titeres/tecnicas/titere-de-guante',
            ],
            [
                'technique'     => lang('Collection.technique_direct_title'),
                'techniqueHref'  => 'museo/coleccion/titeres/tecnicas/manipulacion-directa',
            ],
        ];

        $family = $families[($id - 1) % count($families)];
        $image = 'assets/img/museo/coleccion/titeres/titere.webp';

        return [
            'id'           => $id,
            'pageTitle'    => sprintf('%s #%d', lang('Collection.item_detail_title'), $id),
            'title'        => lang('Collection.mock_puppet_title'),
            'subtitle'     => sprintf(lang('Collection.item_detail_subtitle'), $id),
            'description'  => lang('Collection.item_detail_description'),
            'technique'    => $family['technique'],
            'origin'       => lang('Collection.item_meta_origin_value'),
            'measurements' => lang('Collection.item_meta_measurements_value'),
            'year'         => lang('Collection.item_meta_year_value'),
            'donatedBy'    => lang('Collection.item_meta_donated_by_value'),
            'code'         => sprintf(lang('Collection.item_meta_code_value'), $id),
            'image'        => $image,
            'techniqueHref'=> $family['techniqueHref'],
            'previousHref' => 'museo/coleccion/fichas/' . max(1, $id - 1),
            'nextHref'     => 'museo/coleccion/fichas/' . min(9, $id + 1),
            'related'      => [
                ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/' . $id, 'image' => $image],
                ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/' . (($id % 9) + 1), 'image' => $image],
                ['label' => lang('Collection.mock_puppet_title'), 'href' => 'museo/coleccion/fichas/' . ((($id + 1) % 9) + 1), 'image' => $image],
            ],
        ];
    }
}
