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
        $item = $this->collectionItemBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_item_detail', array_merge(
            $this->pageMeta($item['pageTitle']),
            [
                'nav'  => $this->shellNav(base_url('museo/coleccion/titeres/exhibicion')),
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
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function collectionExhibitCards(): array
    {
        $catalog = $this->collectionItemCatalog();
        $families = $this->collectionItemFamilies();
        $cards = [];

        foreach ($catalog as $index => $item) {
            $family = $families[$index % count($families)];
            $cards[] = [
                'title' => $item['title'],
                'copy'  => lang('Collection.mock_puppet_copy'),
                'href'  => 'museo/coleccion/fichas/' . $item['slug'],
                'image' => 'assets/img/museo/coleccion/titeres/titere.webp',
                'tone'  => $family['tone'],
            ];
        }

        return $cards;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function collectionTechniqueCards(): array
    {
        $tones = ['coral', 'sky', 'violet', 'wine'];
        $cards = [];

        foreach ($this->collectionTechniqueCatalog() as $index => $technique) {
            $cards[] = [
                'title' => $technique['title'],
                'copy'  => lang('Collection.technique_card_copy'),
                'href'  => 'museo/coleccion/titeres/tecnicas/' . $technique['slug'],
                'image' => 'assets/img/museo/coleccion/titeres/titere.webp',
                'tone'  => $tones[$index % count($tones)],
            ];
        }

        return $cards;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function collectionTechniqueBySlug(string $slug): ?array
    {
        $catalog = $this->collectionTechniqueCatalog();

        foreach ($catalog as $index => $technique) {
            if ($technique['slug'] !== $slug) {
                continue;
            }

            $previous = $catalog[($index - 1 + count($catalog)) % count($catalog)];
            $next = $catalog[($index + 1) % count($catalog)];
            $itemCatalog = $this->collectionItemCatalog();
            $relatedIndexes = [
                $index % count($itemCatalog),
                ($index + 3) % count($itemCatalog),
                ($index + 6) % count($itemCatalog),
            ];
            $related = [];

            foreach ($relatedIndexes as $relatedIndex) {
                $related[] = [
                    'label' => $itemCatalog[$relatedIndex]['title'],
                    'href'  => 'museo/coleccion/fichas/' . $itemCatalog[$relatedIndex]['slug'],
                ];
            }

            return [
                'pageTitle'   => $technique['title'],
                'title'       => $technique['title'],
                'subtitle'    => sprintf(lang('Collection.technique_detail_subtitle_template'), $technique['title']),
                'description' => sprintf(lang('Collection.technique_detail_description_template'), $technique['title']),
                'image'       => 'assets/img/museo/coleccion/titeres/titere.webp',
                'related'     => $related,
                'ctaLabel'    => lang('Collection.collection_exhibit'),
                'ctaHref'     => 'museo/coleccion/titeres/exhibicion',
                'previousHref' => 'museo/coleccion/titeres/tecnicas/' . $previous['slug'],
                'nextHref'     => 'museo/coleccion/titeres/tecnicas/' . $next['slug'],
            ];
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function collectionItemBySlug(string $slug): ?array
    {
        $catalog = $this->collectionItemCatalog();
        $index = null;

        if (ctype_digit($slug)) {
            $legacyIndex = (int) $slug - 1;
            if (isset($catalog[$legacyIndex])) {
                $index = $legacyIndex;
            }
        } else {
            foreach ($catalog as $catalogIndex => $item) {
                if ($item['slug'] === $slug) {
                    $index = $catalogIndex;
                    break;
                }
            }
        }

        if ($index === null) {
            return null;
        }

        $item = $catalog[$index];
        $families = $this->collectionItemFamilies();
        $family = $families[$index % count($families)];
        $image = 'assets/img/museo/coleccion/titeres/titere.webp';
        $previous = $catalog[($index - 1 + count($catalog)) % count($catalog)];
        $next = $catalog[($index + 1) % count($catalog)];
        $relatedIndexes = [
            $index,
            ($index + 3) % count($catalog),
            ($index + 6) % count($catalog),
        ];

        return [
            'slug'         => $item['slug'],
            'pageTitle'    => sprintf('%s - %s', lang('Collection.item_detail_title'), $item['title']),
            'title'        => $item['title'],
            'subtitle'     => sprintf(lang('Collection.item_detail_subtitle'), $item['title']),
            'description'  => lang('Collection.item_detail_description'),
            'technique'    => $family['technique'],
            'origin'       => lang('Collection.item_meta_origin_value'),
            'measurements' => lang('Collection.item_meta_measurements_value'),
            'year'         => lang('Collection.item_meta_year_value'),
            'donatedBy'    => lang('Collection.item_meta_donated_by_value'),
            'code'         => sprintf(lang('Collection.item_meta_code_value'), $index + 1),
            'image'        => $image,
            'techniqueHref'=> $family['techniqueHref'],
            'previousHref' => 'museo/coleccion/fichas/' . $previous['slug'],
            'nextHref'     => 'museo/coleccion/fichas/' . $next['slug'],
            'related'      => [
                ['label' => $catalog[$relatedIndexes[0]]['title'], 'href' => 'museo/coleccion/fichas/' . $catalog[$relatedIndexes[0]]['slug'], 'image' => $image],
                ['label' => $catalog[$relatedIndexes[1]]['title'], 'href' => 'museo/coleccion/fichas/' . $catalog[$relatedIndexes[1]]['slug'], 'image' => $image],
                ['label' => $catalog[$relatedIndexes[2]]['title'], 'href' => 'museo/coleccion/fichas/' . $catalog[$relatedIndexes[2]]['slug'], 'image' => $image],
            ],
        ];
    }

    /**
     * @return list<array{slug:string, title:string}>
     */
    private function collectionItemCatalog(): array
    {
        return [
            ['slug' => 'lucia', 'title' => 'Lucía'],
            ['slug' => 'don-cristobal', 'title' => 'Don Cristóbal'],
            ['slug' => 'mariana', 'title' => 'Mariana'],
            ['slug' => 'isidora', 'title' => 'Isidora'],
            ['slug' => 'mateo', 'title' => 'Mateo'],
            ['slug' => 'sofia', 'title' => 'Sofía'],
            ['slug' => 'tomasa', 'title' => 'Tomasa'],
            ['slug' => 'atalia', 'title' => 'Atalia'],
            ['slug' => 'emilio', 'title' => 'Emilio'],
        ];
    }

    /**
     * @return list<array{technique:string, techniqueHref:string, tone:string}>
     */
    private function collectionItemFamilies(): array
    {
        return [
            [
                'technique'    => lang('Collection.technique_hilo_title'),
                'techniqueHref'=> 'museo/coleccion/titeres/tecnicas/titere-de-hilo',
                'tone'         => 'coral',
            ],
            [
                'technique'    => lang('Collection.technique_guante_title'),
                'techniqueHref'=> 'museo/coleccion/titeres/tecnicas/titere-de-guante',
                'tone'         => 'sky',
            ],
            [
                'technique'    => lang('Collection.technique_direct_title'),
                'techniqueHref'=> 'museo/coleccion/titeres/tecnicas/manipulacion-directa',
                'tone'         => 'violet',
            ],
        ];
    }

    /**
     * @return list<array{slug:string, title:string}>
     */
    private function collectionTechniqueCatalog(): array
    {
        return [
            ['slug' => 'titere-de-guante', 'title' => lang('Collection.technique_guante_title')],
            ['slug' => 'titere-de-hilo', 'title' => lang('Collection.technique_hilo_title')],
            ['slug' => 'titere-bocon', 'title' => lang('Collection.technique_bocon_title')],
            ['slug' => 'titere-marote', 'title' => lang('Collection.technique_marote_title')],
            ['slug' => 'titere-gigante', 'title' => lang('Collection.technique_gigante_title')],
            ['slug' => 'titere-de-sombra', 'title' => lang('Collection.technique_sombra_title')],
            ['slug' => 'titere-de-varilla', 'title' => lang('Collection.technique_varilla_title')],
            ['slug' => 'titere-plano', 'title' => lang('Collection.technique_plano_title')],
            ['slug' => 'titere-de-dedo', 'title' => lang('Collection.technique_dedo_title')],
            ['slug' => 'titere-corporal', 'title' => lang('Collection.technique_corporal_title')],
            ['slug' => 'manipulacion-directa', 'title' => lang('Collection.technique_direct_title')],
            ['slug' => 'titere-ventrilocuo', 'title' => lang('Collection.technique_ventrilocuo_title')],
            ['slug' => 'caja-lambe-lambe', 'title' => lang('Collection.technique_lambe_lambe_title')],
            ['slug' => 'titeres-en-cine-stop-motion', 'title' => lang('Collection.technique_stop_motion_title')],
        ];
    }
}
