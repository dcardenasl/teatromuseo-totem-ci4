<?php

namespace App\Controllers;

class TotemController extends BaseController
{
    private ?\App\Services\TotemApiService $apiService = null;

    private function api(): \App\Services\TotemApiService
    {
        if ($this->apiService === null) {
            $this->apiService = new \App\Services\TotemApiService();
        }
        return $this->apiService;
    }

    public function index()
    {
        return view('totem/splash', $this->pageMeta(lang('Splash.welcome')));
    }

    public function language()
    {
        $from = $this->request->getGet('from');
        return view('totem/language', array_merge(
            $this->pageMeta(lang('Menu.select_language')),
            ['from' => $from]
        ));
    }

    public function mainMenu()
    {
        return view('totem/main_menu', array_merge(
            $this->pageMeta(lang('Nav.main_menu')),
            [
                'nav' => $this->shellNav(base_url('/')),
                'items' => [
                    $this->menuItem(lang('Menu.museum'), 'museo', lang('Menu.museum_copy'), 'menu-card--museum', 'menu/menu_museo.webp'),
                    $this->menuItem(lang('Menu.school'), 'teatro-escuela', lang('Menu.school_copy'), 'menu-card--school', 'menu/menu_escuela.webp'),
                    $this->menuItem(lang('Menu.programming'), 'cartelera', lang('Menu.programming_copy'), 'menu-card--programming', 'menu/menu_programacion.webp'),
                    $this->menuItem(lang('Menu.visits'), 'visitas-guiadas', lang('Menu.visits_copy'), 'menu-card--visits', 'menu/menu_visitas.webp'),
                    $this->menuItem(lang('Menu.friends'), 'amigos-de-teatromuseo', lang('Menu.friends_copy'), 'menu-card--friends', 'menu/menu_amigos.webp'),
                ],
            ]
        ));
    }
    public function museum()
    {
        return view('totem/museum_menu', array_merge(
            $this->pageMeta(lang('Menu.explore_museum')),
            [
                'nav' => $this->shellNav(base_url('menu')),
                'exploreLabel' => lang('Menu.explore_museum'),
                'items' => [
                    $this->menuItem(lang('Menu.collection'), 'museo/coleccion', lang('Menu.collection_copy'), 'menu-card--museum', 'museum/cat_coleccion.webp'),
                    $this->menuItem(lang('Menu.comic_history'), 'museo/historia-comica', lang('Menu.comic_history_copy'), 'menu-card--history', 'museum/cat_historia_comica.webp'),
                    $this->menuItem(lang('Menu.el_museo'), 'museo/el-museo', lang('Menu.el_museo_copy'), 'menu-card--school', 'museum/cat_el_museo.webp'),
                    $this->menuItem(lang('Menu.visits'), 'visitas-guiadas', lang('Menu.visits_copy'), 'menu-card--visits', 'museum/cat_visitas_guiadas.webp'),
                ],
            ]
        ));
    }

    public function collectionMain()
    {
        return view('totem/collection_main', array_merge($this->pageMeta(lang('Collection.main_title')), ['nav' => $this->shellNav(base_url('museo'))]));
    }

    public function collectionTechniques()
    {
        return view('totem/collection_techniques', array_merge(
            $this->pageMeta(lang('Collection.techniques_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
                'techniques' => $this->api()->techniques()
            ]
        ));
    }

    public function collectionTechnique($slug)
    {
        $techniques = $this->api()->techniques();
        $technique = null;
        foreach ($techniques as $t) {
            if (isset($t['slug']) && $t['slug'] === $slug) {
                $technique = $this->api()->technique((int)$t['id']);
                break;
            }
        }
        
        if (!$technique) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_technique_detail', array_merge(
            $this->pageMeta(lang('Collection.technique_prefix') . ' - ' . $technique['title']),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion/titeres')),
                'technique' => $technique
            ]
        ));
    }

    public function collectionMasks()
    {
        $traditions = [
            ['title' => 'Comedia del Arte', 'slug' => 'comedia-arte'],
            ['title' => 'Comedia del Andes', 'slug' => 'comedia-andes'],
        ];

        return view('totem/collection_masks', array_merge(
            $this->pageMeta(lang('Collection.masks_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
                'traditions' => $traditions
            ]
        ));
    }

    public function collectionClowns()
    {
        return view('totem/collection_clowns', array_merge(
            $this->pageMeta(lang('Collection.clowns_title')),
            ['nav' => $this->shellNav(base_url('museo/coleccion'))]
        ));
    }

    public function collectionItem($id)
    {
        return view('totem/collection_item_detail', array_merge(
            $this->pageMeta(lang('Collection.item_title') . ' - ' . $id),
            ['nav' => $this->shellNav(base_url('museo/coleccion')), 'id' => $id]
        ));
    }


    public function museumComicHistoryMain()
    {
        return view('totem/comic_history_main', array_merge(
            $this->pageMeta(lang('ComicHistory.main_title')),
            [
                'nav' => $this->shellNav(base_url('museo')),
                'posts' => $this->api()->collection(['category' => 'historia-comica']) // Ajustar según endpoint real de historia
            ]
        ));
    }

    public function museumHistoryPost($slug)
    {
        return view('totem/comic_history_post', array_merge(
            $this->pageMeta(lang('ComicHistory.main_title')),
            [
                'nav' => $this->shellNav(base_url('museo/historia-comica')),
                'post' => $this->api()->museumHistory($slug)
            ]
        ));
    }

    public function museumInfoMain()
    {
        return view('totem/museum_info_main', array_merge($this->pageMeta(lang('Menu.museum')), ['nav' => $this->shellNav(base_url('museo'))]));
    }

    public function museumBuilding()
    {
        return view('totem/museum_building', array_merge(
            $this->pageMeta(lang('MuseumInfo.building_title')),
            [
                'nav' => $this->shellNav(base_url('museo/el-museo')),
                'data' => $this->api()->museum()
            ]
        ));
    }

    public function museumInstitution()
    {
        return view('totem/museum_institution', array_merge(
            $this->pageMeta(lang('MuseumInfo.institution_title')),
            [
                'nav' => $this->shellNav(base_url('museo/el-museo')),
                'data' => $this->api()->museum()
            ]
        ));
    }

    public function museumToday()
    {
        $museum = $this->api()->museum();

        return view('totem/museum_today', array_merge(
            $this->pageMeta(lang('MuseumInfo.main_title')),
            [
                'nav' => $this->shellNav(base_url('museo/el-museo')),
                'today' => $this->buildMuseumTodayContext($museum),
            ]
        ));
    }

    public function extensionContact()
    {
        return view('totem/extension_contact', array_merge($this->pageMeta(lang('Extension.title')), ['nav' => $this->shellNav(base_url('menu'))]));
    }

    private function collectionSection(): array
    {
        return [
            'nav' => $this->shellNav(base_url('museo')),
            'section' => [
                'eyebrow' => lang('Collection.main_title'),
                'title' => 'Colección',
                'copy' => 'Explora nuestro catálogo de títeres, marionetas, máscaras e historia cómica. Selecciona una categoría para ver los objetos en exhibición.',
                'visualClass' => 'section-hero__visual section-hero__visual--museum',
                'detailsTitle' => 'Títeres, Máscaras y Payasos',
                'detailsCopy' => 'Navega por las piezas físicas que forman parte de la muestra viva del teatro. Podrás explorar por técnica de títere o tradición de máscara.',
                'stats' => [
                    ['label' => 'Títeres', 'value' => 'Categoría principal'],
                    ['label' => 'Máscaras', 'value' => 'Tradiciones vivas'],
                    ['label' => 'Payasos', 'value' => 'Historia editorial'],
                ],
            ],
        ];
    }

    private function comicHistorySection(): array
    {
        return [
            'nav' => $this->shellNav(base_url('museo')),
            'section' => [
                'eyebrow' => 'Memoria del Circo y Clown',
                'title' => 'Historia Cómica',
                'copy' => 'Un viaje por la historia del Circo Chileno y el Teatro de Payasos. Recorre la línea de tiempo de nuestro patrimonio cómico.',
                'visualClass' => 'section-hero__visual section-hero__visual--history',
                'detailsTitle' => 'Línea de Tiempo del Humor',
                'detailsCopy' => 'Desde el circo tradicional del siglo XIX hasta las escuelas modernas de clown. La historia de la risa como resistencia cultural.',
                'stats' => [
                    ['label' => 'Formato', 'value' => 'Línea de tiempo'],
                    ['label' => 'Contenido', 'value' => 'Editorial e histórico'],
                    ['label' => 'Hitos', 'value' => 'Circo y Payasos'],
                ],
            ],
        ];
    }

    private function museumInfoSection(): array
    {
        return [
            'nav' => $this->shellNav(base_url('museo')),
            'section' => [
                'eyebrow' => 'Sobre el Espacio',
                'title' => 'El Museo',
                'copy' => 'Conoce la historia institucional, la memoria de nuestro edificio patrimonial (Iglesia San Judas Tadeo) y los logros del FMIM 2024.',
                'visualClass' => 'section-hero__visual section-hero__visual--school',
                'detailsTitle' => 'Historia en la Actualidad',
                'detailsCopy' => 'Nuestra misión es preservar y mediar el oficio del títere y el payaso. Descubre cómo el edificio sirvió como refugio y resistencia.',
                'stats' => [
                    ['label' => 'Edificio', 'value' => 'Patrimonio de Valparaíso'],
                    ['label' => 'Misión', 'value' => 'Preservación y risa'],
                    ['label' => 'FMIM 2024', 'value' => 'Hito de renovación'],
                ],
            ],
        ];
    }

    /**
     * Build a resilient editorial context for the museum today screen.
     *
     * @param array<string, mixed> $museum
     * @return array<string, mixed>
     */
    private function buildMuseumTodayContext(array $museum): array
    {
        $page = isset($museum['page']) && is_array($museum['page']) ? $museum['page'] : [];
        $blocks = isset($museum['blocks']) && is_array($museum['blocks']) ? $this->normalizeMuseumTodayBlocks($museum['blocks']) : [];

        if ($blocks === []) {
            $blocks = [
                [
                    'index' => '01',
                    'title' => lang('MuseumInfo.today_empty_title'),
                    'copy' => lang('MuseumInfo.today_empty_copy'),
                    'fallback' => true,
                ],
            ];
        }

        $sectionTitle = is_string($page['title'] ?? null) && trim((string) $page['title']) !== ''
            ? trim((string) $page['title'])
            : lang('MuseumInfo.main_title');

        $primary = $blocks[0];
        $secondary = array_slice($blocks, 1);

        return [
            'eyebrow' => lang('MuseumInfo.today_eyebrow'),
            'intro' => lang('MuseumInfo.today_intro'),
            'headline' => lang('MuseumInfo.today_title'),
            'image' => 'assets/img/museum/cat_el_museo.webp',
            'imageAlt' => lang('MuseumInfo.today_image_alt'),
            'sectionTitle' => $sectionTitle,
            'primary' => $primary,
            'blocks' => $secondary,
            'stats' => [
                [
                    'label' => lang('MuseumInfo.today_stat_blocks'),
                    'value' => str_pad((string) count($blocks), 2, '0', STR_PAD_LEFT),
                ],
                [
                    'label' => lang('MuseumInfo.today_stat_section'),
                    'value' => $sectionTitle,
                ],
                [
                    'label' => lang('MuseumInfo.today_stat_focus'),
                    'value' => (string) ($primary['title'] ?? $sectionTitle),
                ],
            ],
            'actions' => [
                [
                    'label' => lang('MuseumInfo.today_cta_building'),
                    'href' => base_url('museo/el-museo/edificio'),
                ],
                [
                    'label' => lang('MuseumInfo.today_cta_institution'),
                    'href' => base_url('museo/el-museo/institucion'),
                ],
                [
                    'label' => lang('MuseumInfo.today_cta_back'),
                    'href' => base_url('museo/el-museo'),
                ],
            ],
        ];
    }

    /**
     * @param list<array<string, mixed>> $blocks
     * @return list<array<string, mixed>>
     */
    private function normalizeMuseumTodayBlocks(array $blocks): array
    {
        $normalized = [];

        foreach ($blocks as $index => $block) {
            if (!is_array($block)) {
                continue;
            }

            $title = trim((string) ($block['title'] ?? ''));
            $copy = $this->excerptMuseumBlockContent((string) ($block['content'] ?? ''), 180);

            if ($title === '' && $copy === '') {
                continue;
            }

            $normalized[] = [
                'index' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'title' => $title !== '' ? $title : lang('MuseumInfo.today_blocks_heading'),
                'copy' => $copy !== '' ? $copy : lang('MuseumInfo.today_intro'),
                'fallback' => false,
            ];
        }

        return array_slice($normalized, 0, 4);
    }

    private function excerptMuseumBlockContent(string $html, int $limit = 180): string
    {
        $plain = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        if ($plain === '') {
            return '';
        }

        if (function_exists('mb_strlen') && mb_strlen($plain) > $limit) {
            return rtrim((string) mb_substr($plain, 0, $limit - 1)) . '…';
        }

        if (strlen($plain) > $limit) {
            return rtrim(substr($plain, 0, $limit - 1)) . '…';
        }

        return $plain;
    }

    public function history()
    {
        return view('totem/section', array_merge($this->pageMeta(lang('Menu.history')), $this->historySection()));
    }

    public function theaterSchool()
    {
        return view('totem/section', array_merge($this->pageMeta(lang('Menu.school')), $this->schoolSection()));
    }

    public function billboard()
    {
        return view('totem/billboard', array_merge($this->pageMeta(lang('Menu.programming')), $this->billboardSection()));
    }

    public function billboardDetail($slug = null)
    {
        return view('totem/billboard_detail', array_merge($this->pageMeta(lang('Menu.billboard_detail')), $this->billboardDetailSection($slug)));
    }

    public function guidedVisits()
    {
        return view('totem/section', array_merge($this->pageMeta(lang('Menu.visits')), $this->visitsSection()));
    }

    public function friends()
    {
        return view('totem/section', array_merge($this->pageMeta(lang('Menu.friends')), $this->friendsSection()));
    }

    private function pageMeta(string $title): array
    {
        return [
            'pageTitle' => 'Teatromuseo - ' . $title,
            'bodyClass' => 'totem-app',
            'htmlLang' => $this->request->getLocale(),
        ];
    }

    private function menuItem(string $title, string $href, string $copy, string $class, string $img = ''): array
    {
        return [
            'title' => $title,
            'href' => base_url($href),
            'copy' => $copy,
            'class' => $class,
            'img' => $img ? 'assets/img/' . $img : '',
        ];
    }

    private function shellNav(?string $backHref = null): array
    {
        $currentUri = uri_string();
        return [
            [
                'label' => lang('Nav.back'),
                'href' => $backHref ?? base_url('menu'),
                'icon' => '←',
                'class' => 'pill-button pill-button--back',
            ],
            [
                'label' => lang('Nav.lang'),
                'href' => base_url('language' . ($currentUri ? '?from=' . urlencode($currentUri) : '')),
                'icon' => '◌',
                'class' => 'pill-button pill-button--lang',
            ],
            [
                'label' => lang('Nav.home'),
                'href' => base_url('/'),
                'icon' => '⌂',
                'class' => 'pill-button pill-button--home',
            ],
        ];
    }

    private function museumSection(): array
    {
        return [
            'nav' => $this->shellNav(),
            'section' => [
                'eyebrow' => lang('Collection.main_title'),
                'title' => lang('Menu.explore_museum'),
                'copy' => 'Una vitrina editorial para piezas, objetos y archivos del teatro patrimonial. La propuesta prioriza contraste, marcos ornamentales y lectura rápida para una experiencia táctil en tótem.',
                'visualClass' => 'section-hero__visual section-hero__visual--museum',
                'detailsTitle' => 'Capas de experiencia',
                'detailsCopy' => 'La pantalla mezcla descubrimiento, memoria y navegación por capas. Cada bloque actúa como una pieza de museo con jerarquía clara y alto contraste.',
                'stats' => [
                    ['label' => 'Formato', 'value' => 'Recorrido curado'],
                    ['label' => 'Enfoque', 'value' => 'Patrimonio y mediación'],
                    ['label' => 'Acción', 'value' => 'Tocar para explorar'],
                ],
            ],
        ];
    }

    private function historySection(): array
    {
        return [
            'nav' => $this->shellNav(base_url('menu')),
            'section' => [
                'eyebrow' => lang('Menu.history_copy'),
                'title' => lang('Menu.history'),
                'copy' => 'Un relato visual más calmado, con piezas enmarcadas y una lectura de archivo que conserva el carácter artesanal de la propuesta.',
                'visualClass' => 'section-hero__visual section-hero__visual--history',
                'detailsTitle' => 'Línea de tiempo',
                'detailsCopy' => 'Conviene trabajar este módulo como una secuencia de hitos con tarjetas compactas, para no perder el ritmo de escaneo en pantalla vertical.',
                'stats' => [
                    ['label' => 'Origen', 'value' => 'Fundación y contexto'],
                    ['label' => 'Tono', 'value' => 'Documental y cercano'],
                    ['label' => 'Salida', 'value' => 'Volver al menú'],
                ],
            ],
        ];
    }

    private static function getMonthName(int $monthNum, string $locale): string
    {
        $months = [
            'es' => [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            'en' => [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'fr' => [1 => 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            'pt' => [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        ];
        return $months[$locale][$monthNum] ?? $months['es'][$monthNum] ?? '';
    }

    private function schoolSection(): array
    {
        $apiCourses = $this->api()->courses();
        $courses = [];

        if (!empty($apiCourses)) {
            foreach ($apiCourses as $course) {
                $tag = lang('Menu.school_category_education');
                if (isset($course['school_category_id'])) {
                    $catId = (int)$course['school_category_id'];
                    if ($catId === 1) {
                        $tag = lang('Menu.school_category_workshop');
                    } elseif ($catId === 2) {
                        $tag = lang('Menu.school_category_plays');
                    } elseif ($catId === 3) {
                        $tag = lang('Menu.school_category_education');
                    }
                }
                
                $startText = '';
                if (!empty($course['start_date'])) {
                    $time = strtotime($course['start_date']);
                    $day = date('d', $time);
                    $monthName = self::getMonthName((int)date('n', $time), $this->request->getLocale());
                    $year = date('Y', $time);
                    $locale = $this->request->getLocale();

                    if ($locale === 'en') {
                        $startText = 'Starts: ' . $monthName . ' ' . $day . ', ' . $year;
                    } elseif ($locale === 'fr') {
                        $startText = 'Début: ' . $day . ' ' . $monthName . ' ' . $year;
                    } elseif ($locale === 'pt') {
                        $startText = 'Início: ' . $day . ' de ' . $monthName . ' de ' . $year;
                    } else {
                        $startText = 'Inicio: ' . $day . ' de ' . $monthName . ' de ' . $year;
                    }
                }

                $courses[] = [
                    'tag'   => $tag,
                    'title' => $course['title'] ?? '',
                    'start' => $startText,
                    'copy'  => $course['description'] ?? '',
                ];
            }
        } else {
            // Mocks de contingencia
            $courses = [
                [
                    'tag' => 'Nacional',
                    'title' => 'La Escuela de los Nuevos Comediantes',
                    'start' => 'Inicio: 20 de abril de 2026',
                    'copy' => 'Formación escénica para jóvenes y adultos con foco en presencia, oficio y repertorio.',
                ],
                [
                    'tag' => 'Para niños',
                    'title' => 'Súbete al escenario',
                    'start' => 'Inicio: 20 de abril de 2026',
                    'copy' => 'Sesiones lúdicas para descubrir escena, juego y expresión corporal desde el teatro.',
                ],
                [
                    'tag' => 'Internacional',
                    'title' => 'Máscaras Sagradas',
                    'start' => 'Inicio: 20 de abril de 2026',
                    'copy' => 'Práctica de presencia y construcción corporal con énfasis en ritualidad y máscara.',
                ],
            ];
        }

        return [
            'nav' => $this->shellNav(),
            'section' => [
                'eyebrow' => lang('Menu.school_copy'),
                'title' => lang('Menu.school'),
                'copy' => 'Diseñado como una página viva con cursos, fechas, categorías y contacto. La prioridad es que la información larga se entienda sin esfuerzo y conserve el clima editorial de la propuesta.',
                'visualClass' => 'section-hero__visual section-hero__visual--school',
                'detailsTitle' => 'Próximos cursos',
                'detailsCopy' => 'Cada ficha debe sostenerse con una etiqueta, una fecha de inicio y un resumen corto. Si el contenido crece, la estructura sigue siendo legible en scroll.',
                'stats' => [
                    ['label' => 'Escuela', 'value' => 'Talleres y formación'],
                    ['label' => 'Duración', 'value' => 'Ciclos cortos'],
                    ['label' => 'Contacto', 'value' => 'teatroescuela@teatromuseo.cl'],
                ],
            ],
            'courses' => $courses,
        ];
    }

    private function billboardSection(): array
    {
        $apiShows = $this->api()->shows();
        $months = [];
        $events = [];
        $locale = $this->request->getLocale();

        if (!empty($apiShows)) {
            $monthsMap = [];
            foreach ($apiShows as $show) {
                if (!empty($show['start_date'])) {
                    $time = strtotime($show['start_date']);
                    $monthName = self::getMonthName((int)date('n', $time), $locale);
                    $day = date('j', $time);
                    $monthsMap[$monthName][] = $day;
                }

                $tag = lang('Menu.audience_family');
                if (isset($show['audience_id'])) {
                    $audId = (int)$show['audience_id'];
                    if ($audId === 1) {
                        $tag = lang('Menu.audience_national');
                    } elseif ($audId === 2) {
                        $tag = lang('Menu.audience_international');
                    } elseif ($audId === 3) {
                        $tag = lang('Menu.audience_kids');
                    } elseif ($audId === 4) {
                        $tag = lang('Menu.audience_general');
                    }
                }

                // Determinar clase de tarjeta por la audiencia
                $class = 'event-card--family';
                if ($tag === 'Adultos') {
                    $class = 'event-card--adult';
                }

                $events[] = [
                    'tag'   => $tag,
                    'type'  => 'Teatro', // Valor por defecto en la estructura
                    'title' => $show['title'] ?? '',
                    'copy'  => $show['description'] ?? '',
                    'class' => $class,
                    'slug'  => $show['slug'] ?? ($show['id'] ?? '1'),
                ];
            }

            foreach ($monthsMap as $title => $days) {
                $months[] = [
                    'title' => $title,
                    'days'  => array_unique($days),
                ];
            }
        } else {
            // Mocks de contingencia
            $months = [
                ['title' => 'Mayo', 'days' => ['10', '17', '24', '30']],
                ['title' => 'Junio', 'days' => ['2', '9', '16', '23']],
            ];
            $events = [
                [
                    'tag' => 'Familiar',
                    'type' => 'Títeres',
                    'title' => 'La Malattia di Nogasto',
                    'copy' => 'Una comedia física con clowns y malabares que apuesta por el asombro y el ritmo de la escena.',
                    'class' => 'event-card--family',
                    'slug' => 'la-malattia-di-nogasto',
                ],
                [
                    'tag' => 'Adultos',
                    'type' => 'Máscaras',
                    'title' => 'Muaki',
                    'copy' => 'Una propuesta de cuerpo, suspensión y juego con una visualidad frontal y directa.',
                    'class' => 'event-card--adult',
                    'slug' => 'muaki',
                ],
                [
                    'tag' => 'Familiar',
                    'type' => 'Payasos',
                    'title' => 'Ayayai',
                    'copy' => 'Escena física con humor, música y objetos para público de todas las edades.',
                    'class' => 'event-card--family',
                    'slug' => 'ayayai',
                ],
                [
                    'tag' => 'Adultos',
                    'type' => 'Música',
                    'title' => 'Rock festival',
                    'copy' => 'Una programación nocturna con energía de escena en vivo y lenguaje de concierto.',
                    'class' => 'event-card--music',
                    'slug' => 'rock-festival',
                ],
            ];
        }

        return [
            'nav' => $this->shellNav(),
            'months' => $months,
            'events' => $events,
        ];
    }

    private function billboardDetailSection($slug = null): array
    {
        $title = 'La Malattia di Nogasto';
        $copy = 'Una comedia física y clownesca construida para el tótem: lectura inmediata, bloques de información bien separados y una imagen central protagonista. El texto largo debe convivir con fichas rápidas y una señal clara para obtener más información.';
        $tags = ['Adultos', 'Máscaras'];
        
        if ($slug === 'muaki') {
            $title = 'Muaki';
            $copy = 'Una propuesta de cuerpo, suspensión y juego con una visualidad frontal y directa. Máscara cómica y comedia del arte se entrelazan de manera magistral.';
            $tags = ['Adultos', 'Máscaras'];
        } elseif ($slug === 'ayayai') {
            $title = 'Ayayai';
            $copy = 'Escena física con humor, música y objetos para público de todas las edades. Risas, música en vivo y juego gestual garantizados.';
            $tags = ['Familiar', 'Payasos'];
        } elseif ($slug === 'rock-festival') {
            $title = 'Rock festival';
            $copy = 'Una programación nocturna con energía de escena en vivo y lenguaje de concierto para jóvenes y adultos en Teatromuseo.';
            $tags = ['Adultos', 'Música'];
        }

        return [
            'nav' => $this->shellNav(base_url('cartelera')),
            'detail' => [
                'tags' => $tags,
                'title' => $title,
                'company' => 'Compañía Teatromuseo',
                'direction' => 'Dirección: Víctor Quiroga',
                'date' => 'Sábado 10 de mayo',
                'time' => '19.00 h',
                'duration' => '50 min aprox.',
                'price' => 'General: $4.500',
                'copy' => $copy,
            ],
        ];
    }

    private function visitsSection(): array
    {
        return [
            'nav' => $this->shellNav(),
            'section' => [
                'eyebrow' => lang('Menu.visits_copy'),
                'title' => lang('Menu.visits'),
                'copy' => 'Una variante más breve del módulo de museo, útil para grupos y reservas. Sirve para recuperar el lenguaje ornamental de la propuesta con una acción clara.',
                'visualClass' => 'section-hero__visual section-hero__visual--visits',
                'detailsTitle' => 'Cómo funciona',
                'detailsCopy' => 'Bloques cortos, tarjetas por tipo de recorrido y una llamada a reserva para que la navegación sea inmediata desde pantalla táctil.',
                'stats' => [
                    ['label' => 'Reservas', 'value' => 'Con anticipación'],
                    ['label' => 'Público', 'value' => 'Escolar y general'],
                    ['label' => 'Modalidad', 'value' => 'Presencial'],
                ],
            ],
        ];
    }

    private function friendsSection(): array
    {
        return [
            'nav' => $this->shellNav(),
            'section' => [
                'eyebrow' => lang('Menu.friends_copy'),
                'title' => lang('Menu.friends'),
                'copy' => 'Un espacio para alianzas, aportes y pertenencia. En la interfaz conviene mantener una composición generosa, con pocos elementos y una invitación clara a participar.',
                'visualClass' => 'section-hero__visual section-hero__visual--friends',
                'detailsTitle' => 'Participa',
                'detailsCopy' => 'El bloque debe funcionar como un puente entre identidad institucional y llamado a la acción, sin saturar la pantalla con texto.',
                'stats' => [
                    ['label' => 'Aporte', 'value' => 'Colaboraciones'],
                    ['label' => 'Red', 'value' => 'Comunidad cultural'],
                    ['label' => 'Contacto', 'value' => 'info@teatromuseo.cl'],
                ],
            ],
        ];
    }
}
