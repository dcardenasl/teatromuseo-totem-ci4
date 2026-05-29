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
        return view('totem/splash', $this->pageMeta('Bienvenido'));
    }

    public function language()
    {
        $from = $this->request->getGet('from');
        return view('totem/language', array_merge(
            $this->pageMeta('Selecciona tu idioma'),
            ['from' => $from]
        ));
    }

    public function mainMenu()
    {
        return view('totem/main_menu', array_merge(
            $this->pageMeta(lang('Totem.menu.main_menu')),
            [
                'nav' => $this->shellNav(base_url('/')),
                'items' => [
                    $this->menuItem(lang('Totem.menu.museum'), 'museo', lang('Totem.menu.museum_copy'), 'menu-card--museum'),
                    $this->menuItem(lang('Totem.menu.history'), 'historia', lang('Totem.menu.history_copy'), 'menu-card--history'),
                    $this->menuItem(lang('Totem.menu.school'), 'teatro-escuela', lang('Totem.menu.school_copy'), 'menu-card--school'),
                    $this->menuItem(lang('Totem.menu.programming'), 'cartelera', lang('Totem.menu.programming_copy'), 'menu-card--programming'),
                    $this->menuItem(lang('Totem.menu.visits'), 'visitas-guiadas', lang('Totem.menu.visits_copy'), 'menu-card--visits'),
                    $this->menuItem(lang('Totem.menu.friends'), 'amigos-de-teatromuseo', lang('Totem.menu.friends_copy'), 'menu-card--friends'),
                ],
            ]
        ));
    }
    public function museum()
    {
        return view('totem/museum_menu', array_merge(
            $this->pageMeta(lang('Totem.menu.explore_museum')),
            [
                'nav' => $this->shellNav(base_url('menu')),
                'items' => [
                    $this->menuItem(lang('Totem.menu.collection'), 'museo/coleccion', lang('Totem.menu.collection_copy'), 'menu-card--museum'),
                    $this->menuItem(lang('Totem.menu.comic_history'), 'museo/historia-comica', lang('Totem.menu.comic_history_copy'), 'menu-card--history'),
                    $this->menuItem(lang('Totem.menu.el_museo'), 'museo/el-museo', lang('Totem.menu.el_museo_copy'), 'menu-card--school'),
                    $this->menuItem(lang('Totem.menu.visits'), 'visitas-guiadas', lang('Totem.menu.visits_copy'), 'menu-card--visits'),
                ],
            ]
        ));
    }

    public function collectionMain()
    {
        return view('totem/collection_main', array_merge($this->pageMeta('Colección'), ['nav' => $this->shellNav(base_url('museo'))]));
    }

    public function collectionTechniques()
    {
        return view('totem/collection_techniques', array_merge($this->pageMeta('Técnicas'), ['nav' => $this->shellNav(base_url('museo/coleccion'))]));
    }

    public function collectionTechnique($slug)
    {
        return view('totem/collection_technique_detail', array_merge($this->pageMeta('Técnica - ' . $slug), ['nav' => $this->shellNav(base_url('museo/coleccion/titeres'))]));
    }

    public function collectionMasks()
    {
        return view('totem/collection_masks', array_merge($this->pageMeta('Máscaras'), ['nav' => $this->shellNav(base_url('museo/coleccion'))]));
    }

    public function collectionMaskTradition($slug)
    {
        return view('totem/collection_mask_tradition_detail', array_merge($this->pageMeta('Tradición - ' . $slug), ['nav' => $this->shellNav(base_url('museo/coleccion/mascaras'))]));
    }

    public function collectionClowns()
    {
        return view('totem/collection_clowns', array_merge($this->pageMeta('Payasos'), ['nav' => $this->shellNav(base_url('museo/coleccion'))]));
    }

    public function collectionItem($id)
    {
        return view('totem/collection_item_detail', array_merge($this->pageMeta('Ficha - ' . $id), ['nav' => $this->shellNav(base_url('museo/coleccion'))]));
    }

    public function museumComicHistoryMain()
    {
        return view('totem/comic_history_main', array_merge($this->pageMeta('Historia Cómica'), ['nav' => $this->shellNav(base_url('museo'))]));
    }

    public function museumHistoryPost($slug)
    {
        return view('totem/comic_history_post', array_merge($this->pageMeta('Post - ' . $slug), ['nav' => $this->shellNav(base_url('museo/historia-comica'))]));
    }

    public function museumInfoMain()
    {
        return view('totem/museum_info_main', array_merge($this->pageMeta('El Museo'), ['nav' => $this->shellNav(base_url('museo'))]));
    }

    public function museumBuilding()
    {
        return view('totem/museum_building', array_merge($this->pageMeta('Edificio'), ['nav' => $this->shellNav(base_url('museo/el-museo'))]));
    }

    public function museumInstitution()
    {
        return view('totem/museum_institution', array_merge($this->pageMeta('Institución'), ['nav' => $this->shellNav(base_url('museo/el-museo'))]));
    }

    public function museumToday()
    {
        return view('totem/museum_today', array_merge($this->pageMeta('Actualidad'), ['nav' => $this->shellNav(base_url('museo/el-museo'))]));
    }

    public function extensionContact()
    {
        return view('totem/extension_contact', array_merge($this->pageMeta('Contacto'), ['nav' => $this->shellNav(base_url('menu'))]));
    }

    private function collectionSection(): array
    {
        return [
            'nav' => $this->shellNav(base_url('museo')),
            'section' => [
                'eyebrow' => 'Recorre la colección viva',
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

    public function history()
    {
        return view('totem/section', array_merge($this->pageMeta('Historia'), $this->historySection()));
    }

    public function theaterSchool()
    {
        return view('totem/section', array_merge($this->pageMeta('Teatro escuela'), $this->schoolSection()));
    }

    public function billboard()
    {
        return view('totem/billboard', array_merge($this->pageMeta('Cartelera'), $this->billboardSection()));
    }

    public function billboardDetail()
    {
        return view('totem/billboard_detail', array_merge($this->pageMeta('Detalle de cartelera'), $this->billboardDetailSection()));
    }

    public function guidedVisits()
    {
        return view('totem/section', array_merge($this->pageMeta('Visitas guiadas'), $this->visitsSection()));
    }

    public function friends()
    {
        return view('totem/section', array_merge($this->pageMeta('Amigos de Teatromuseo'), $this->friendsSection()));
    }

    private function pageMeta(string $title): array
    {
        return [
            'pageTitle' => 'Teatromuseo - ' . $title,
            'bodyClass' => 'totem-app',
            'htmlLang' => 'es',
        ];
    }

    private function menuItem(string $title, string $href, string $copy, string $class): array
    {
        return [
            'title' => $title,
            'href' => base_url($href),
            'copy' => $copy,
            'class' => $class,
        ];
    }

    private function shellNav(?string $backHref = null): array
    {
        $currentUri = uri_string();
        return [
            [
                'label' => lang('Totem.nav.back'),
                'href' => $backHref ?? base_url('menu'),
                'icon' => '←',
                'class' => 'pill-button pill-button--back',
            ],
            [
                'label' => lang('Totem.nav.lang'),
                'href' => base_url('language' . ($currentUri ? '?from=' . urlencode($currentUri) : '')),
                'icon' => '◌',
                'class' => 'pill-button pill-button--lang',
            ],
            [
                'label' => lang('Totem.nav.home'),
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
                'eyebrow' => 'Recorre la colección viva',
                'title' => 'Explora el museo',
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
                'eyebrow' => 'Memoria y origen',
                'title' => 'Historia',
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
            $locale = $this->request->getLocale();
            foreach ($apiCourses as $course) {
                $tag = 'Formación';
                if (isset($course['school_category_id'])) {
                    $catId = (int)$course['school_category_id'];
                    if ($catId === 1) {
                        $tag = $locale === 'en' ? 'Workshop' : ($locale === 'fr' ? 'Atelier' : ($locale === 'pt' ? 'Oficina' : 'Taller'));
                    } elseif ($catId === 2) {
                        $tag = $locale === 'en' ? 'School Plays' : ($locale === 'fr' ? 'Pièces' : ($locale === 'pt' ? 'Peças' : 'Obras'));
                    } elseif ($catId === 3) {
                        $tag = $locale === 'en' ? 'Education' : ($locale === 'fr' ? 'Formation' : ($locale === 'pt' ? 'Formação' : 'Formación'));
                    }
                }
                
                $startText = '';
                if (!empty($course['start_date'])) {
                    $time = strtotime($course['start_date']);
                    $day = date('d', $time);
                    $monthName = self::getMonthName((int)date('n', $time), $locale);
                    $year = date('Y', $time);
                    
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
                'eyebrow' => 'Formación y mediación',
                'title' => 'Teatro escuela',
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

                $tag = 'Familiar';
                if (isset($show['audience_id'])) {
                    $audId = (int)$show['audience_id'];
                    if ($audId === 1) {
                        $tag = $locale === 'en' ? 'National' : 'Nacional';
                    } elseif ($audId === 2) {
                        $tag = $locale === 'en' ? 'International' : 'Internacional';
                    } elseif ($audId === 3) {
                        $tag = $locale === 'en' ? 'For Kids' : ($locale === 'fr' ? 'Pour Enfants' : ($locale === 'pt' ? 'Para Crianças' : 'Para Niños'));
                    } elseif ($audId === 4) {
                        $tag = $locale === 'en' ? 'General' : 'General';
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
                ],
                [
                    'tag' => 'Adultos',
                    'type' => 'Máscaras',
                    'title' => 'Muaki',
                    'copy' => 'Una propuesta de cuerpo, suspensión y juego con una visualidad frontal y directa.',
                    'class' => 'event-card--adult',
                ],
                [
                    'tag' => 'Familiar',
                    'type' => 'Payasos',
                    'title' => 'Ayayai',
                    'copy' => 'Escena física con humor, música y objetos para público de todas las edades.',
                    'class' => 'event-card--family',
                ],
                [
                    'tag' => 'Adultos',
                    'type' => 'Música',
                    'title' => 'Rock festival',
                    'copy' => 'Una programación nocturna con energía de escena en vivo y lenguaje de concierto.',
                    'class' => 'event-card--music',
                ],
            ];
        }

        return [
            'nav' => $this->shellNav(),
            'months' => $months,
            'events' => $events,
        ];
    }

    private function billboardDetailSection(): array
    {
        return [
            'nav' => $this->shellNav(base_url('cartelera')),
            'detail' => [
                'tags' => ['Adultos', 'Máscaras'],
                'title' => 'La Malattia di Nogasto',
                'company' => 'Compañía Teatromuseo',
                'direction' => 'Dirección: Víctor Quiroga',
                'date' => 'Sábado 10 de mayo',
                'time' => '19.00 h',
                'duration' => '50 min aprox.',
                'price' => 'General: $4.500',
                'copy' => 'Una comedia física y clownesca construida para el tótem: lectura inmediata, bloques de información bien separados y una imagen central protagonista. El texto largo debe convivir con fichas rápidas y una señal clara para obtener más información.',
            ],
        ];
    }

    private function visitsSection(): array
    {
        return [
            'nav' => $this->shellNav(),
            'section' => [
                'eyebrow' => 'Recorridos y mediación',
                'title' => 'Visitas guiadas',
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
                'eyebrow' => 'Comunidad y apoyo',
                'title' => 'Amigos de Teatromuseo',
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
