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
                'nav'        => $this->shellNav(base_url('museo/coleccion')),
                'tabs'       => $this->collectionTabs('techniques'),
                'techniques' => $this->collectionTechniqueCards(),
            ]
        ));
    }

    public function collectionPuppetsExhibit(): string
    {
        return view('totem/collection_puppets_exhibit', array_merge(
            $this->pageMeta(lang('Collection.puppets_exhibit_title')),
            [
                'nav'   => $this->shellNav(base_url('museo/coleccion')),
                'tabs'  => $this->collectionTabs('exhibit'),
                'items' => $this->collectionExhibitCards(),
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
                'title'     => '',
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
        $item = $this->collectionItemBySlug($slug);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_item_detail', array_merge(
            $this->pageMeta($item['pageTitle']),
            [
                'nav'   => $this->shellNav(base_url('museo/coleccion/titeres/exhibicion')),
                'title' => lang('Collection.puppets'),
                'item'  => $item,
            ]
        ));
    }

    // -------------------------------------------------------------------------
    // i18n helpers
    // -------------------------------------------------------------------------

    /**
     * Returns the active locale, falling back to 'es'.
     */
    private function getLocale(): string
    {
        $locale = service('request')->getLocale();

        return in_array($locale, ['es', 'en', 'fr', 'pt'], true) ? $locale : 'es';
    }

    /**
     * Extracts a localised string from a scalar or multilingual array.
     *
     * JSON fields can be either a plain string (legacy/placeholder) or an object
     * like {"es":"…","en":"…","fr":"…","pt":"…"}. This helper normalises both.
     *
     * @param mixed $value
     */
    private function localized(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            $locale = $this->getLocale();

            return (string) ($value[$locale] ?? $value['es'] ?? '');
        }

        return '';
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
                'label'  => lang('Collection.collection_exhibit'),
                'href'   => 'museo/coleccion/titeres/exhibicion',
                'active' => $active === 'exhibit',
            ],
            [
                'label'  => lang('Collection.collection_techniques'),
                'href'   => 'museo/coleccion/titeres/tecnicas',
                'active' => $active === 'techniques',
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Cards
    // -------------------------------------------------------------------------

    /**
     * @return list<array<string, mixed>>
     */
    private function collectionExhibitCards(): array
    {
        $tones = ['coral', 'sky', 'violet', 'wine'];
        $cards = [];

        foreach ($this->loadTiteresMock() as $index => $titere) {
            $slug  = strtolower($titere['codigo_vitrina_bodega']);
            $title = $titere['nombre'] ?? $titere['codigo_vitrina_bodega'];
            $copy  = $titere['descripcion_corta'] !== null
                ? $this->localized($titere['descripcion_corta'])
                : lang('Collection.mock_puppet_copy');
            $image = $titere['imagen_portada'] ?? 'assets/img/museo/coleccion/titeres/titere.webp';

            $cards[] = [
                'title' => $title,
                'copy'  => $copy,
                'href'  => 'museo/coleccion/fichas/' . $slug,
                'image' => $image,
                'tone'  => $tones[$index % count($tones)],
            ];
        }

        return $cards;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function collectionTechniqueCards(): array
    {
        $tones   = ['coral', 'sky', 'violet', 'wine'];
        $bySlug  = [];

        foreach ($this->loadTecnicasMock() as $t) {
            $bySlug[$t['slug']] = $t;
        }

        $cards = [];

        foreach ($this->collectionTechniqueCatalog() as $index => $technique) {
            $mock  = $bySlug[$technique['slug']] ?? null;
            $copy  = $mock !== null
                ? $this->localized($mock['copy'])
                : lang('Collection.technique_card_copy');
            $image = $mock['image'] ?? 'assets/img/museo/coleccion/titeres/titere.webp';

            $cards[] = [
                'title' => $technique['title'],
                'copy'  => $copy,
                'href'  => 'museo/coleccion/titeres/tecnicas/' . $technique['slug'],
                'image' => $image,
                'tone'  => $tones[$index % count($tones)],
            ];
        }

        return $cards;
    }

    // -------------------------------------------------------------------------
    // Detail builders
    // -------------------------------------------------------------------------

    /**
     * @return array<string, mixed>|null
     */
    private function collectionTechniqueBySlug(string $slug): ?array
    {
        $catalog = $this->collectionTechniqueCatalog();
        $bySlug  = [];

        foreach ($this->loadTecnicasMock() as $t) {
            $bySlug[$t['slug']] = $t;
        }

        foreach ($catalog as $index => $technique) {
            if ($technique['slug'] !== $slug) {
                continue;
            }

            $mock     = $bySlug[$slug] ?? null;
            $previous = $catalog[($index - 1 + count($catalog)) % count($catalog)];
            $next     = $catalog[($index + 1) % count($catalog)];

            // Related: titeres that use this technique
            $related = [];

            foreach ($this->loadTiteresMock() as $titere) {
                if (count($related) >= 3) {
                    break;
                }

                $tSlug = $this->techniqueNameToSlug($titere['tecnicas_asociadas'] ?? '');

                if ($tSlug === $slug) {
                    $related[] = [
                        'label' => $titere['nombre'] ?? $titere['codigo_vitrina_bodega'],
                        'href'  => 'museo/coleccion/fichas/' . strtolower($titere['codigo_vitrina_bodega']),
                    ];
                }
            }

            // Fallback: first 3 items from catalog
            if (empty($related)) {
                $itemCatalog = $this->collectionItemCatalog();

                for ($i = 0; $i < 3 && $i < count($itemCatalog); $i++) {
                    $related[] = [
                        'label' => $itemCatalog[$i]['title'],
                        'href'  => 'museo/coleccion/fichas/' . $itemCatalog[$i]['slug'],
                    ];
                }
            }

            return [
                'pageTitle'    => $technique['title'],
                'title'        => $technique['title'],
                'subtitle'     => $mock !== null
                    ? $this->localized($mock['subtitle'])
                    : sprintf(lang('Collection.technique_detail_subtitle_template'), $technique['title']),
                'description'  => $mock !== null
                    ? $this->localized($mock['description'])
                    : sprintf(lang('Collection.technique_detail_description_template'), $technique['title']),
                'image'        => $mock['image'] ?? 'assets/img/museo/coleccion/titeres/titere.webp',
                'related'      => $related,
                'ctaLabel'     => lang('Collection.collection_exhibit'),
                'ctaHref'      => 'museo/coleccion/titeres/exhibicion',
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
        $allTiteres = $this->loadTiteresMock();
        $found      = null;
        $foundIndex = null;

        foreach ($allTiteres as $index => $titere) {
            $itemSlug = strtolower($titere['codigo_vitrina_bodega']);

            if ($slug === $itemSlug || (ctype_digit($slug) && (int) $slug === $titere['numero_coleccion'])) {
                $found      = $titere;
                $foundIndex = $index;
                break;
            }
        }

        if ($found === null || $foundIndex === null) {
            return null;
        }

        $total         = count($allTiteres);
        $prevTitere    = $allTiteres[($foundIndex - 1 + $total) % $total];
        $nextTitere    = $allTiteres[($foundIndex + 1) % $total];
        $techniqueSlug = $this->techniqueNameToSlug($found['tecnicas_asociadas'] ?? '');

        $title       = $found['nombre'] ?? $found['codigo_vitrina_bodega'];
        $subtitle    = $found['descripcion_corta'] !== null
            ? $this->localized($found['descripcion_corta'])
            : sprintf(lang('Collection.item_detail_subtitle'), $title);
        $description = $found['descripcion_fisica'] !== null
            ? $this->localized($found['descripcion_fisica'])
            : ($found['descripcion_corta'] !== null
                ? $this->localized($found['descripcion_corta'])
                : lang('Collection.item_detail_description'));
        $image       = $found['imagen_portada'] ?? 'assets/img/museo/coleccion/titeres/titere.webp';

        // Related: the 3 items that follow (wrapping)
        $related = [];

        for ($i = 1; $i <= 3; $i++) {
            $rt = $allTiteres[($foundIndex + $i) % $total];
            $related[] = [
                'label' => $rt['nombre'] ?? $rt['codigo_vitrina_bodega'],
                'href'  => 'museo/coleccion/fichas/' . strtolower($rt['codigo_vitrina_bodega']),
                'image' => $rt['imagen_portada'] ?? 'assets/img/museo/coleccion/titeres/titere.webp',
            ];
        }

        return [
            'slug'          => strtolower($found['codigo_vitrina_bodega']),
            'pageTitle'     => sprintf('%s — %s', $title, lang('Collection.item_detail_title')),
            'title'         => $title,
            'subtitle'      => $subtitle,
            'description'   => $description,
            'technique'     => $this->techniqueSlugToTitle($techniqueSlug),
            'techniqueHref' => 'museo/coleccion/titeres/tecnicas/' . $techniqueSlug,
            'origin'        => $found['origen'] ?? '—',
            'measurements'  => $found['tamanio'] ?? '—',
            'year'          => $found['periodo'] ?? '—',
            'donatedBy'     => $found['donado_facilitado_por'] ?? '—',
            'code'          => $found['codigo_vitrina_bodega'],
            'image'         => $image,
            'previousHref'  => 'museo/coleccion/fichas/' . strtolower($prevTitere['codigo_vitrina_bodega']),
            'nextHref'      => 'museo/coleccion/fichas/' . strtolower($nextTitere['codigo_vitrina_bodega']),
            'related'       => $related,
        ];
    }

    // -------------------------------------------------------------------------
    // Catalogs
    // -------------------------------------------------------------------------

    /**
     * @return list<array{slug:string, title:string}>
     */
    private function collectionItemCatalog(): array
    {
        $catalog = [];

        foreach ($this->loadTiteresMock() as $titere) {
            $catalog[] = [
                'slug'  => strtolower($titere['codigo_vitrina_bodega']),
                'title' => $titere['nombre'] ?? $titere['codigo_vitrina_bodega'],
            ];
        }

        return $catalog;
    }

    /**
     * @return list<array{slug:string, title:string}>
     */
    private function collectionTechniqueCatalog(): array
    {
        return [
            ['slug' => 'titere-de-guante',            'title' => lang('Collection.technique_guante_title')],
            ['slug' => 'titere-de-hilo',               'title' => lang('Collection.technique_hilo_title')],
            ['slug' => 'titere-bocon',                 'title' => lang('Collection.technique_bocon_title')],
            ['slug' => 'titere-marote',                'title' => lang('Collection.technique_marote_title')],
            ['slug' => 'titere-gigante',               'title' => lang('Collection.technique_gigante_title')],
            ['slug' => 'titere-de-sombra',             'title' => lang('Collection.technique_sombra_title')],
            ['slug' => 'titere-de-varilla',            'title' => lang('Collection.technique_varilla_title')],
            ['slug' => 'titere-plano',                 'title' => lang('Collection.technique_plano_title')],
            ['slug' => 'titere-de-dedo',               'title' => lang('Collection.technique_dedo_title')],
            ['slug' => 'titere-corporal',              'title' => lang('Collection.technique_corporal_title')],
            ['slug' => 'manipulacion-directa',         'title' => lang('Collection.technique_direct_title')],
            ['slug' => 'titere-ventrilocuo',           'title' => lang('Collection.technique_ventrilocuo_title')],
            ['slug' => 'caja-lambe-lambe',             'title' => lang('Collection.technique_lambe_lambe_title')],
            ['slug' => 'titeres-en-cine-stop-motion',  'title' => lang('Collection.technique_stop_motion_title')],
        ];
    }

    // -------------------------------------------------------------------------
    // Mock data loaders
    // -------------------------------------------------------------------------

    /**
     * @return list<array<string, mixed>>
     */
    private function loadTiteresMock(): array
    {
        static $cache = null;

        if ($cache === null) {
            $path  = APPPATH . 'Data/titeres_mock.json';
            $cache = json_decode((string) file_get_contents($path), true) ?? [];
        }

        return $cache;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadTecnicasMock(): array
    {
        static $cache = null;

        if ($cache === null) {
            $path  = APPPATH . 'Data/tecnicas_mock.json';
            $cache = json_decode((string) file_get_contents($path), true) ?? [];
        }

        return $cache;
    }

    // -------------------------------------------------------------------------
    // Technique name ↔ slug helpers
    // -------------------------------------------------------------------------

    /**
     * Maps the human-readable technique name stored in titeres_mock.json
     * (field: tecnicas_asociadas) to the URL slug used in routes.
     */
    private function techniqueNameToSlug(string $name): string
    {
        $map = [
            'Guante'       => 'titere-de-guante',
            'Hilo'         => 'titere-de-hilo',
            'Bocón'        => 'titere-bocon',
            'Marote'       => 'titere-marote',
            'Gigante'      => 'titere-gigante',
            'Sombra'       => 'titere-de-sombra',
            'Varilla'      => 'titere-de-varilla',
            'Plano'        => 'titere-plano',
            'Dedo'         => 'titere-de-dedo',
            'Corporal'     => 'titere-corporal',
            'Directa'      => 'manipulacion-directa',
            'Ventrílocuo'  => 'titere-ventrilocuo',
            'Lambe Lambe'  => 'caja-lambe-lambe',
            'Stop Motion'  => 'titeres-en-cine-stop-motion',
        ];

        return $map[trim($name)] ?? 'titere-de-guante';
    }

    /**
     * Returns the i18n-aware display title for a technique slug.
     */
    private function techniqueSlugToTitle(string $slug): string
    {
        $map = [
            'titere-de-guante'            => lang('Collection.technique_guante_title'),
            'titere-de-hilo'              => lang('Collection.technique_hilo_title'),
            'titere-bocon'                => lang('Collection.technique_bocon_title'),
            'titere-marote'               => lang('Collection.technique_marote_title'),
            'titere-gigante'              => lang('Collection.technique_gigante_title'),
            'titere-de-sombra'            => lang('Collection.technique_sombra_title'),
            'titere-de-varilla'           => lang('Collection.technique_varilla_title'),
            'titere-plano'                => lang('Collection.technique_plano_title'),
            'titere-de-dedo'              => lang('Collection.technique_dedo_title'),
            'titere-corporal'             => lang('Collection.technique_corporal_title'),
            'manipulacion-directa'        => lang('Collection.technique_direct_title'),
            'titere-ventrilocuo'          => lang('Collection.technique_ventrilocuo_title'),
            'caja-lambe-lambe'            => lang('Collection.technique_lambe_lambe_title'),
            'titeres-en-cine-stop-motion' => lang('Collection.technique_stop_motion_title'),
        ];

        return $map[$slug] ?? lang('Collection.technique_guante_title');
    }
}
