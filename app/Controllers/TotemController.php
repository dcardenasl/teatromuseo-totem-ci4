<?php

namespace App\Controllers;

class TotemController extends BaseController
{
    private const MUSEUM_BLOCK_EXCERPT_CHARS = 180;
    private const MUSEUM_TODAY_MAX_BLOCKS    = 4;

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

        if (is_string($from) && $from !== '' && ! preg_match('/^[a-z0-9\/\-]+$/', $from)) {
            $from = '';
        }

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
            $this->pageMeta(lang('Menu.el_museo')),
            [
                'nav' => $this->shellNav(base_url('menu')),
                'exploreLabel' => lang('Menu.explore_museum'),
                'items' => [
                    $this->menuItem(lang('Menu.collection'), 'museo/coleccion', lang('Menu.collection_copy'), 'menu-card--museum', 'museum/cat_coleccion.webp'),
                    $this->menuItem(lang('Menu.comic_history'), 'museo/historia', lang('Menu.comic_history_copy'), 'menu-card--history', 'museum/cat_historia_comica.webp'),
                    $this->menuItem(lang('Menu.explore_museum'), 'museo/el-museo', lang('Menu.museum_copy'), 'menu-card--school', 'museum/cat_el_museo.webp'),
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
                'techniques' => $this->api()->techniques(),
            ]
        ));
    }

    public function collectionPuppetsExhibit()
    {
        return view('totem/collection_puppets_exhibit', array_merge(
            $this->pageMeta(lang('Collection.puppets_exhibit_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
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
                'nav' => $this->shellNav(base_url('museo/coleccion/titeres/tecnicas')),
                'technique' => $technique,
            ]
        ));
    }

    public function collectionMasksExhibit()
    {
        return view('totem/collection_masks_exhibit', array_merge(
            $this->pageMeta(lang('Collection.masks_exhibit_title')),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion')),
            ]
        ));
    }

    public function collectionMasksTraditions()
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

    public function collectionMaskTradition($slug)
    {
        $traditions = [
            'comedia-arte' => lang('Collection.tradition_comedia_arte'),
            'comedia-andes' => lang('Collection.tradition_comedia_andes'),
        ];

        if (! isset($traditions[$slug])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('totem/collection_mask_tradition', array_merge(
            $this->pageMeta($traditions[$slug]),
            [
                'nav' => $this->shellNav(base_url('museo/coleccion/mascaras/tradiciones')),
                'tradition' => [
                    'slug' => $slug,
                    'title' => $traditions[$slug],
                ],
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

    public function museumHistoryMain()
    {
        return view('totem/comic_history_main', array_merge(
            $this->pageMeta(lang('ComicHistory.main_title')),
            [
                'nav' => $this->shellNav(base_url('museo')),
            ]
        ));
    }

    public function museumComicHistoryMain()
    {
        return $this->museumHistoryMain();
    }

    public function museumHistoryPost($slug)
    {
        return view('totem/comic_history_post', array_merge(
            $this->pageMeta(lang('ComicHistory.main_title')),
            [
                'nav' => $this->shellNav(base_url('museo/historia')),
                'post' => $this->api()->museumHistory($slug),
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
                'data' => $this->api()->museum(),
            ]
        ));
    }

    public function museumInstitution()
    {
        return view('totem/museum_institution', array_merge(
            $this->pageMeta(lang('MuseumInfo.institution_title')),
            [
                'nav' => $this->shellNav(base_url('museo/el-museo')),
                'data' => $this->api()->museum(),
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
            $copy = $this->excerptMuseumBlockContent((string) ($block['content'] ?? ''));

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

        return array_slice($normalized, 0, self::MUSEUM_TODAY_MAX_BLOCKS);
    }

    private function excerptMuseumBlockContent(string $html, int $limit = self::MUSEUM_BLOCK_EXCERPT_CHARS): string
    {
        $plain = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        if ($plain === '') {
            return '';
        }

        if (mb_strlen($plain) > $limit) {
            return rtrim((string) mb_substr($plain, 0, $limit - 1)) . '…';
        }

        return $plain;
    }

    public function theaterSchool()
    {
        return view('totem/theater_school', array_merge($this->pageMeta(lang('Menu.school')), $this->schoolSection()));
    }

    public function billboard()
    {
        return view('totem/billboard', array_merge($this->pageMeta(lang('Menu.programming')), $this->billboardSection()));
    }

    public function billboardDetail($slug = null)
    {
        return view('totem/billboard_detail', array_merge($this->pageMeta(lang('Menu.billboard_detail')), $this->billboardDetailSection($slug)));
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
                'href' => base_url('menu'),
                'icon' => '⌂',
                'class' => 'pill-button pill-button--home',
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
        $locale = $this->request->getLocale();
        $fallbackMonthName = self::getMonthName(4, $locale);
        $fallbackStart = match ($locale) {
            'en' => sprintf(lang('Section.school_start_en'), $fallbackMonthName, '20', '2026'),
            'fr' => sprintf(lang('Section.school_start_fr'), '20', $fallbackMonthName, '2026'),
            'pt' => sprintf(lang('Section.school_start_pt'), '20', $fallbackMonthName, '2026'),
            default => sprintf(lang('Section.school_start_es'), '20', $fallbackMonthName, '2026'),
        };

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
                    if ($locale === 'en') {
                        $startText = sprintf(lang('Section.school_start_en'), $monthName, $day, $year);
                    } elseif ($locale === 'fr') {
                        $startText = sprintf(lang('Section.school_start_fr'), $day, $monthName, $year);
                    } elseif ($locale === 'pt') {
                        $startText = sprintf(lang('Section.school_start_pt'), $day, $monthName, $year);
                    } else {
                        $startText = sprintf(lang('Section.school_start_es'), $day, $monthName, $year);
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
                    'tag' => lang('Section.school_course_tag'),
                    'title' => lang('Section.school_course_title'),
                    'start' => $fallbackStart,
                    'copy' => lang('Section.school_course_copy'),
                ],
                [
                    'tag' => lang('Menu.audience_kids'),
                    'title' => lang('Section.school_course_child_title'),
                    'start' => $fallbackStart,
                    'copy' => lang('Section.school_course_child_copy'),
                ],
                [
                    'tag' => lang('Menu.audience_international'),
                    'title' => lang('Section.school_course_mask_title'),
                    'start' => $fallbackStart,
                    'copy' => lang('Section.school_course_mask_copy'),
                ],
            ];
        }

        // El tótem muestra un curso a la vez por restricción de espacio en pantalla
        $courses = array_slice($courses, 0, 1);

        $personPhoto = 'assets/img/school/school_collage.webp';
        $teachers = [
            [
                'tone' => 'teacher-card--amber',
                'name' => 'Marta Jara',
                'role' => lang('Section.teacher_marta_role'),
                'description' => lang('Section.teacher_marta_desc'),
            ],
            [
                'tone' => 'teacher-card--navy',
                'name' => 'Tomás Vega',
                'role' => lang('Section.teacher_tomas_role'),
                'description' => lang('Section.teacher_tomas_desc'),
            ],
            [
                'tone' => 'teacher-card--olive',
                'name' => 'Paula Montt',
                'role' => lang('Section.teacher_paula_role'),
                'description' => lang('Section.teacher_paula_desc'),
            ],
            [
                'tone' => 'teacher-card--sepia',
                'name' => 'Nicolás Ríos',
                'role' => lang('Section.teacher_nicolas_role'),
                'description' => lang('Section.teacher_nicolas_desc'),
            ],
            [
                'tone' => 'teacher-card--rose',
                'name' => 'Valentina Soto',
                'role' => lang('Section.teacher_valentina_role'),
                'description' => lang('Section.teacher_valentina_desc'),
            ],
            [
                'tone' => 'teacher-card--crimson',
                'name' => 'Javier Lobo',
                'role' => lang('Section.teacher_javier_role'),
                'description' => lang('Section.teacher_javier_desc'),
            ],
            [
                'tone' => 'teacher-card--gold',
                'name' => 'Camila Figueroa',
                'role' => lang('Section.teacher_camila_role'),
                'description' => lang('Section.teacher_camila_desc'),
            ],
            [
                'tone' => 'teacher-card--ink',
                'name' => 'Rodrigo Salas',
                'role' => lang('Section.teacher_rodrigo_role'),
                'description' => lang('Section.teacher_rodrigo_desc'),
            ],
        ];

        $students = [
            [
                'tone' => 'teacher-card--amber',
                'name' => 'Lucía Paredes',
                'role' => lang('Section.student_lucia_role'),
                'description' => lang('Section.student_lucia_desc'),
            ],
            [
                'tone' => 'teacher-card--navy',
                'name' => 'Diego Araya',
                'role' => lang('Section.student_diego_role'),
                'description' => lang('Section.student_diego_desc'),
            ],
            [
                'tone' => 'teacher-card--olive',
                'name' => 'Sofía Núñez',
                'role' => lang('Section.student_sofia_role'),
                'description' => lang('Section.student_sofia_desc'),
            ],
            [
                'tone' => 'teacher-card--sepia',
                'name' => 'Benjamín Toro',
                'role' => lang('Section.student_benjamin_role'),
                'description' => lang('Section.student_benjamin_desc'),
            ],
            [
                'tone' => 'teacher-card--rose',
                'name' => 'Antonia Rojas',
                'role' => lang('Section.student_antonia_role'),
                'description' => lang('Section.student_antonia_desc'),
            ],
            [
                'tone' => 'teacher-card--crimson',
                'name' => 'Matías Bravo',
                'role' => lang('Section.student_matias_role'),
                'description' => lang('Section.student_matias_desc'),
            ],
            [
                'tone' => 'teacher-card--gold',
                'name' => 'Elena Cárdenas',
                'role' => lang('Section.student_elena_role'),
                'description' => lang('Section.student_elena_desc'),
            ],
            [
                'tone' => 'teacher-card--ink',
                'name' => 'Felipe Muñoz',
                'role' => lang('Section.student_felipe_role'),
                'description' => lang('Section.student_felipe_desc'),
            ],
        ];

        return [
            'nav' => $this->shellNav(),
            'section' => [
                'title' => lang('Menu.school'),
                'heroImage' => 'assets/img/menu/menu_escuela.webp',
                'heroAlt' => lang('Section.school_hero_alt'),
                'introCopy' => lang('Section.school_intro'),
                'stats' => [
                    ['label' => lang('Section.school_stat_courses'), 'value' => '50'],
                    ['label' => lang('Section.school_stat_teachers'), 'value' => '20'],
                    ['label' => lang('Section.school_stat_students'), 'value' => '1000'],
                ],
                'teachersTitle' => lang('Section.school_teachers_title'),
                'studentsTitle' => lang('Section.school_students_title'),
                'coursesTitle' => lang('Section.school_courses_title'),
                'courseImage' => 'assets/img/menu/menu_programacion.webp',
                'courseTag' => lang('Section.school_course_tag'),
                'courseTitle' => lang('Section.school_course_title'),
                'courseStart' => $fallbackStart,
                'courseCopy' => lang('Section.school_course_copy'),
                'courseContactLabel' => lang('Section.course_contact_label'),
                'courseContact' => lang('Section.school_course_contact_value'),
                'courseQrLabel' => lang('Section.school_course_qr_label'),
                'courseQrImage' => 'assets/img/school/teatroescuela-qr.png',
                'courseQrUrl' => 'https://teatromuseo.cl/teatro-escuela?utm_source=totem',
                'closingImage' => 'assets/img/school/school_collage.webp',
                'logoPrimary' => 'assets/img/logos/ministerio_culturas_chile.png',
                'logoSecondary' => 'assets/img/menu/menu_escuela.webp',
            ],
            'courses' => $courses,
            'teachers' => $teachers,
            'students' => $students,
            'personPhoto' => $personPhoto,
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

                $tag = lang('Billboard.audience_family');
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
                $class = match ($audId) {
                    1 => 'event-card--national',
                    2 => 'event-card--international',
                    3 => 'event-card--kids',
                    4 => 'event-card--adult',
                    default => 'event-card--family',
                };

                $events[] = [
                    'tag'   => $tag,
                    'type'  => lang('Billboard.event_type_theatre'),
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
                ['title' => lang('Billboard.month_may'), 'days' => ['10', '17', '24', '30']],
                ['title' => lang('Billboard.month_june'), 'days' => ['2', '9', '16', '23']],
            ];
            $events = [
                [
                    'tag' => lang('Billboard.fallback_audience_family'),
                    'type' => lang('Billboard.event_type_puppets'),
                    'title' => lang('Billboard.fallback_title_1'),
                    'copy' => lang('Billboard.fallback_copy_1'),
                    'class' => 'event-card--family',
                    'slug' => 'la-malattia-di-nogasto',
                ],
                [
                    'tag' => lang('Billboard.fallback_audience_adults'),
                    'type' => lang('Billboard.event_type_masks'),
                    'title' => lang('Billboard.fallback_title_2'),
                    'copy' => lang('Billboard.fallback_copy_2'),
                    'class' => 'event-card--adult',
                    'slug' => 'muaki',
                ],
                [
                    'tag' => lang('Billboard.fallback_audience_family'),
                    'type' => lang('Billboard.event_type_clowns'),
                    'title' => lang('Billboard.fallback_title_3'),
                    'copy' => lang('Billboard.fallback_copy_3'),
                    'class' => 'event-card--family',
                    'slug' => 'ayayai',
                ],
                [
                    'tag' => lang('Billboard.fallback_audience_adults'),
                    'type' => lang('Billboard.event_type_music'),
                    'title' => lang('Billboard.fallback_title_4'),
                    'copy' => lang('Billboard.fallback_copy_4'),
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
        $title = lang('Billboard.fallback_title_1');
        $copy = lang('Billboard.detail_copy_1');
        $tags = [lang('Billboard.fallback_audience_adults'), lang('Billboard.event_type_masks')];
        $image = 'assets/img/billboard/la-malattia-di-nogasto-poster.webp';
        $closingImage = 'assets/img/billboard/la-malattia-di-nogasto-collage.webp';
        $qrImage = 'assets/img/school/teatroescuela-qr.png';
        $closingNote = lang('Billboard.default_closing_note');

        if ($slug === 'muaki') {
            $title = lang('Billboard.fallback_title_2');
            $copy = lang('Billboard.detail_copy_2');
            $tags = [lang('Billboard.fallback_audience_adults'), lang('Billboard.event_type_masks')];
        } elseif ($slug === 'ayayai') {
            $title = lang('Billboard.fallback_title_3');
            $copy = lang('Billboard.detail_copy_3');
            $tags = [lang('Billboard.fallback_audience_family'), lang('Billboard.event_type_clowns')];
        } elseif ($slug === 'rock-festival') {
            $title = lang('Billboard.fallback_title_4');
            $copy = lang('Billboard.detail_copy_4');
            $tags = [lang('Billboard.fallback_audience_adults'), lang('Billboard.event_type_music')];
        }

        return [
            'nav' => $this->shellNav(base_url('cartelera')),
            'detail' => [
                'tags' => $tags,
                'title' => $title,
                'image' => $image,
                'company' => lang('Billboard.default_company'),
                'direction' => lang('Billboard.default_direction'),
                'date' => lang('Billboard.default_date'),
                'time' => lang('Billboard.default_time'),
                'duration' => lang('Billboard.default_duration'),
                'price' => lang('Billboard.default_price'),
                'copy' => $copy,
                'closingImage' => $closingImage,
                'qrImage' => $qrImage,
                'closingNote' => $closingNote,
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
                'copy' => lang('Section.friends_copy'),
                'visualClass' => 'section-hero__visual section-hero__visual--friends',
                'detailsTitle' => lang('Section.friends_details_title'),
                'detailsCopy' => lang('Section.friends_details_copy'),
                'stats' => [
                    ['label' => lang('Section.friends_stat_support_label'), 'value' => lang('Section.friends_stat_support')],
                    ['label' => lang('Section.friends_stat_network_label'), 'value' => lang('Section.friends_stat_network')],
                    ['label' => lang('Section.friends_stat_contact_label'), 'value' => lang('Section.friends_stat_contact')],
                ],
            ],
        ];
    }
}
