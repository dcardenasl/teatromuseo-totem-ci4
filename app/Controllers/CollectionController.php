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
        $collection = [];
        try {
            $collection = $this->totemApi()->collection();
        } catch (\Exception $e) {
            // Fail silently
        }

        $hasClowns = !empty($collection['payasos'] ?? []);
        $hasMasks  = !empty($collection['mascaras'] ?? []);

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
        $collection = [];
        try {
            $collection = $this->totemApi()->collection();
        } catch (\Exception $e) {
            // Fail silently
        }

        $items = [];
        $tones = ['coral', 'sky', 'violet', 'wine'];
        foreach (($collection['mascaras'] ?? []) as $index => $item) {
            $items[] = [
                'title' => $item['name'] ?? '',
                'copy'  => $item['summary'] ?? '',
                'href'  => 'museo/coleccion/fichas/' . ($item['id'] ?? ''),
                'image' => api_file_url($item['cover_file_id'] ?? null),
                'tone'  => $tones[$index % count($tones)],
            ];
        }

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
        $collection = [];
        try {
            $collection = $this->totemApi()->collection();
        } catch (\Exception $e) {
            // Fail silently
        }

        $items = [];
        $tones = ['coral', 'sky', 'violet', 'wine'];
        foreach (($collection['payasos'] ?? []) as $index => $item) {
            $items[] = [
                'title' => $item['name'] ?? '',
                'copy'  => $item['summary'] ?? '',
                'href'  => 'museo/coleccion/fichas/' . ($item['id'] ?? ''),
                'image' => api_file_url($item['cover_file_id'] ?? null),
                'tone'  => $tones[$index % count($tones)],
            ];
        }

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
        $item = $this->collectionItemBySlug($slug);
        $backUrl = base_url('museo/coleccion/titeres/exhibicion');
        $title = lang('Collection.puppets');

        if ($item === null && is_numeric($slug)) {
            $apiItem = $this->totemApi()->collectionItem((int) $slug);
            if ($apiItem !== []) {
                $categorySlug = $apiItem['category']['slug'] ?? 'titeres';
                $backUrl = base_url("museo/coleccion/{$categorySlug}/exhibicion");

                $title = match ($categorySlug) {
                    'mascaras' => lang('Collection.masks'),
                    'payasos'  => lang('Collection.clowns'),
                    default    => lang('Collection.puppets'),
                };

                $related = [];
                foreach (($apiItem['related_pieces'] ?? []) as $rel) {
                    $related[] = [
                        'label' => $rel['name'] ?? '',
                        'href'  => 'museo/coleccion/fichas/' . ($rel['id'] ?? ''),
                        'image' => api_file_url($rel['cover_file_id'] ?? null),
                    ];
                }

                $item = [
                    'slug'          => $slug,
                    'pageTitle'     => sprintf('%s — %s', $apiItem['name'] ?? '', $this->langStr('Collection.item_detail_title')),
                    'title'         => $apiItem['name'] ?? '',
                    'subtitle'      => $apiItem['summary'] ?? '',
                    'description'   => $apiItem['summary'] ?? '',
                    'technique'     => ! empty($apiItem['techniques']) ? $apiItem['techniques'][0]['name'] : '—',
                    'techniqueHref' => '#',
                    'origin'        => $apiItem['origin'] ?? '—',
                    'measurements'  => '—',
                    'year'          => $apiItem['period'] ?? '—',
                    'donatedBy'     => '—',
                    'code'          => sprintf('TM%04d', $apiItem['id']),
                    'image'         => api_file_url($apiItem['cover_file_id'] ?? null),
                    'previousHref'  => '#',
                    'nextHref'      => '#',
                    'related'       => $related,
                ];
            }
        }

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_item_detail', array_merge(
            $this->pageMeta($item['pageTitle']),
            [
                'nav'   => $this->shellNav($backUrl),
                'title' => $title,
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
        $locale = \Config\Services::request()->getLocale();

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

    /**
     * Returns a translation line as a plain string, joining the plural-form
     * array shape that lang() can return for lines that take a $times argument.
     */
    private function langStr(string $line): string
    {
        $value = lang($line);

        return is_array($value) ? implode(' ', $value) : $value;
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
                'label'  => $this->langStr('Collection.collection_exhibit'),
                'href'   => 'museo/coleccion/titeres/exhibicion',
                'active' => $active === 'exhibit',
            ],
            [
                'label'  => $this->langStr('Collection.collection_techniques'),
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
                    : sprintf($this->langStr('Collection.technique_detail_subtitle_template'), $technique['title']),
                'description'  => $mock !== null
                    ? $this->localized($mock['description'])
                    : sprintf($this->langStr('Collection.technique_detail_description_template'), $technique['title']),
                'image'        => $mock['image'] ?? 'assets/img/museo/coleccion/titeres/titere.webp',
                'related'      => $related,
                'ctaLabel'     => $this->langStr('Collection.collection_exhibit'),
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
            : sprintf($this->langStr('Collection.item_detail_subtitle'), $title);
        $description = $found['descripcion_fisica'] !== null
            ? $this->localized($found['descripcion_fisica'])
            : ($found['descripcion_corta'] !== null
                ? $this->localized($found['descripcion_corta'])
                : $this->langStr('Collection.item_detail_description'));
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
            'pageTitle'     => sprintf('%s — %s', $title, $this->langStr('Collection.item_detail_title')),
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
            ['slug' => 'titere-de-guante',            'title' => $this->langStr('Collection.technique_guante_title')],
            ['slug' => 'titere-de-hilo',               'title' => $this->langStr('Collection.technique_hilo_title')],
            ['slug' => 'titere-bocon',                 'title' => $this->langStr('Collection.technique_bocon_title')],
            ['slug' => 'titere-marote',                'title' => $this->langStr('Collection.technique_marote_title')],
            ['slug' => 'titere-gigante',               'title' => $this->langStr('Collection.technique_gigante_title')],
            ['slug' => 'titere-de-sombra',             'title' => $this->langStr('Collection.technique_sombra_title')],
            ['slug' => 'titere-de-varilla',            'title' => $this->langStr('Collection.technique_varilla_title')],
            ['slug' => 'titere-plano',                 'title' => $this->langStr('Collection.technique_plano_title')],
            ['slug' => 'titere-de-dedo',               'title' => $this->langStr('Collection.technique_dedo_title')],
            ['slug' => 'titere-corporal',              'title' => $this->langStr('Collection.technique_corporal_title')],
            ['slug' => 'manipulacion-directa',         'title' => $this->langStr('Collection.technique_direct_title')],
            ['slug' => 'titere-ventrilocuo',           'title' => $this->langStr('Collection.technique_ventrilocuo_title')],
            ['slug' => 'caja-lambe-lambe',             'title' => $this->langStr('Collection.technique_lambe_lambe_title')],
            ['slug' => 'titeres-en-cine-stop-motion',  'title' => $this->langStr('Collection.technique_stop_motion_title')],
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
            'titere-de-guante'            => $this->langStr('Collection.technique_guante_title'),
            'titere-de-hilo'              => $this->langStr('Collection.technique_hilo_title'),
            'titere-bocon'                => $this->langStr('Collection.technique_bocon_title'),
            'titere-marote'               => $this->langStr('Collection.technique_marote_title'),
            'titere-gigante'              => $this->langStr('Collection.technique_gigante_title'),
            'titere-de-sombra'            => $this->langStr('Collection.technique_sombra_title'),
            'titere-de-varilla'           => $this->langStr('Collection.technique_varilla_title'),
            'titere-plano'                => $this->langStr('Collection.technique_plano_title'),
            'titere-de-dedo'              => $this->langStr('Collection.technique_dedo_title'),
            'titere-corporal'             => $this->langStr('Collection.technique_corporal_title'),
            'manipulacion-directa'        => $this->langStr('Collection.technique_direct_title'),
            'titere-ventrilocuo'          => $this->langStr('Collection.technique_ventrilocuo_title'),
            'caja-lambe-lambe'            => $this->langStr('Collection.technique_lambe_lambe_title'),
            'titeres-en-cine-stop-motion' => $this->langStr('Collection.technique_stop_motion_title'),
        ];

        return $map[$slug] ?? $this->langStr('Collection.technique_guante_title');
    }
}
