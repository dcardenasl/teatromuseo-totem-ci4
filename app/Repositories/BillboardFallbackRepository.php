<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * Provides static fallback data for the billboard screen.
 */
final class BillboardFallbackRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function months(): array
    {
        return [
            ['title' => lang('Billboard.month_june'), 'days' => ['6', '7', '14', '21', '28']],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function events(): array
    {
        return [
            [
                'tag'        => 'Familiar',
                'type'       => 'Teatro de sombras',
                'audience'   => 'Familiar',
                'company'    => 'Catalejo teatro de sombras',
                'title'      => 'Palabras cantadas del viento',
                'copy'       => 'Teatro de sombras para niñeces y público familiar, con una mirada poética sobre los cuatro vientos y la naturaleza.',
                'class'      => 'event-card--family',
                'tone'       => 'event-card--tone-coral',
                'slug'       => 'palabras-cantadas-del-viento',
                'image'      => 'assets/img/billboard/cartelera-card-palabras.webp',
                'dateLabel'  => 'SÁBADO 6 DE JUNIO',
                'timeLabel'  => '12.00 H',
            ],
            [
                'tag'        => 'Familiar',
                'type'       => 'Títeres y marionetas',
                'audience'   => 'Familiar',
                'company'    => 'Trip Teatro',
                'title'      => 'Juan pirquinero y el diablo',
                'copy'       => 'Títeres, máscaras y narración visual para una historia minera en el desierto de Atacama.',
                'class'      => 'event-card--adult',
                'tone'       => 'event-card--tone-sky',
                'slug'       => 'juan-pirquinero-y-el-diablo',
                'image'      => 'assets/img/billboard/cartelera-card-juan.webp',
                'dateLabel'  => 'DOMINGO 7 DE JUNIO',
                'timeLabel'  => '16.30 H',
            ],
            [
                'tag'        => 'Familiar',
                'type'       => 'Títeres',
                'audience'   => 'Familiar',
                'company'    => 'Ojo Piojo',
                'title'      => 'Guardianes del Humedal: Salvando al Arrayan',
                'copy'       => 'Una obra lúdica y ambiental que mezcla títeres bocones, humor y circo para cuidar un humedal del sur de Chile.',
                'class'      => 'event-card--family',
                'tone'       => 'event-card--tone-violet',
                'slug'       => 'guardianes-del-humedal',
                'image'      => 'assets/img/billboard/cartelera-card-guardianes.webp',
                'dateLabel'  => 'DOMINGO 14 DE JUNIO',
                'timeLabel'  => '16.30 H',
            ],
            [
                'tag'        => 'Familiar',
                'type'       => 'Payaso',
                'audience'   => 'Familiar',
                'company'    => 'El Faro',
                'title'      => 'Soquete Bestial 60 años',
                'copy'       => 'Una selección de rutinas de una extensa trayectoria, con humor, interacción y energía de payaso clásico.',
                'class'      => 'event-card--adult',
                'tone'       => 'event-card--tone-wine',
                'slug'       => 'soquete-bestial-60-anos',
                'image'      => 'assets/img/billboard/cartelera-card-soquete.webp',
                'dateLabel'  => 'DOMINGO 21 DE JUNIO',
                'timeLabel'  => '16.30 H',
            ],
            [
                'tag'        => 'Familiar',
                'type'       => 'Teatro Lambe Lambe',
                'audience'   => 'Familiar',
                'company'    => 'Miniteatros',
                'title'      => 'Cómo ser una gotita de agua',
                'copy'       => 'Un viaje divertido y emotivo para redescubrir la identidad de una gotita de agua y volver al ciclo natural.',
                'class'      => 'event-card--family',
                'tone'       => 'event-card--tone-moss',
                'slug'       => 'como-ser-una-gotita-de-agua',
                'image'      => 'assets/img/billboard/cartelera-card-gotita.webp',
                'dateLabel'  => 'DOMINGO 28 DE JUNIO',
                'timeLabel'  => '16.30 H',
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $slug): ?array
    {
        foreach ($this->events() as $event) {
            if (($event['slug'] ?? null) === $slug) {
                return $event;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function detail(string $slug): array
    {
        $event = $this->find($slug) ?? $this->events()[0];

        $copy = $event['copy'] ?? lang('Billboard.detail_copy_1');
        $secondaryCopy = null;

        switch ($event['slug'] ?? '') {
            case 'palabras-cantadas-del-viento':
                $copy = 'Palabras cantadas del viento es una obra de teatro de sombras dirigida a niñeces y público familiar. La propuesta ofrece una mirada poética y contemplativa sobre los cuatro vientos, utilizando la narrativa visual como eje central. La obra relata el ciclo de vida del Pewén, en donde los vientos transportan sus pólenes hasta otros árboles sagrados, propiciando la polinización y el surgimiento de una nueva vida. De este proceso nacen los piñones, semillas que continúan su viaje por las aguas del río, encontrándose con otros seres en la búsqueda de un nuevo lugar para crecer. La experiencia escénica se complementa con otros pequeños momentos inspirados en la naturaleza y una selección de folklore poético, interpretado y musicalizado en vivo. La obra ha sido presentada en diversos espacios educativos y culturales de la comuna de Valdivia y Panguipulli.';
                $secondaryCopy = 'La Compañía Catalejo Teatro de Sombras, fundada en Valdivia el año 2019, desarrolla propuestas escénicas dirigidas a las infancias que integran teatro de sombras y música original en vivo. Su trabajo promueve el asombro, el disfrute artístico y el fomento lector a través de contenidos patrimoniales de tradición oral y divulgación científico-naturalista. Han participado en festivales de ciencias, ferias del libro y diversas instancias culturales y educativas, además de recorrer escuelas rurales con talleres y funciones mediante el Bibliomóvil. Su trayectoria incluye la creación de la obra “Palabras Cantadas del Viento” (2024), fortaleciendo su aporte a la educación artística y cultural de niñas y niños.';
                break;
            case 'juan-pirquinero-y-el-diablo':
                $copy = 'Inspirada en el imaginario colectivo de las leyendas mineras del norte de Chile, esta obra de teatro de títeres nos cuenta de una historia profunda, cargada de simbolismo y humor popular. Ambientada en el árido desierto de Atacama, la puesta en escena nos trae a una pareja de titiriteros juglares que, desde su carromato como soporte y el uso de títeres de diferentes técnicas, máscaras y un dispositivo de narración con imágenes, nos cuentan la historia de Juan, un pirquinero incansable que vive junto a su familia en condiciones de extrema precariedad. Su obsesión por hallar una veta de cobre que le permita sacar a su familia de la pobreza lo conduce a hacer un pacto con el mismísimo diablo, en una decisión desesperada que da paso a una espera llena de tensión por lo que pueda ocurrir cuando el diablo llegue a cobrar lo pactado.';
                $secondaryCopy = 'Comenzó su trabajo profesional el 12 de octubre de 1989. Su misión es defender y difundir el teatro, en especial el teatro de animación como una forma auténtica de la expresión artística y rico patrimonio inmaterial de la humanidad a través del estudio, la práctica y cambio. En estos 27 años, se ha consolidado como una compañía importante de Teatro de Animación de Brasil, presentándose en todas las ciudades capitales cariocas y otros 15 países de 3 continentes (América del Sur, Europa y Asia).';
                break;
            case 'guardianes-del-humedal':
                $copy = 'Guardianes del Humedal: Salvando al Arrayán invita a niñas, niños y familias a proteger la biodiversidad de un colorido humedal del sur de Chile. Ambrosio, el ambicioso, quiere transformar la naturaleza en un proyecto inmobiliario, pero los animales nativos —Huillín, Pudú y Martín Pescador— se unen para salvarlo. La obra combina títeres bocones, humor, juegos titiriteros, efectos especiales analógicos y circo con hulla hop, creando una experiencia lúdica y emocionante. Inspirada en los paisajes de la Araucanía, mezcla creatividad, diversión y conciencia ambiental, mostrando cómo imaginación y colaboración pueden cuidar y transformar nuestro entorno natural.';
                $secondaryCopy = 'La compañía de títeres OjoPiojo, liderada por Raúl Ignacio Hidalgo Sandoval, desarrolla y difunde el arte del títere como un oficio vivo y comunitario. Desde 2016, Raúl ha perfeccionado técnicas como marionetas de hilo, títeres de guante, bocones, sombras y de mesa, combinando materiales reciclados, madera, papel maché y goma espuma. La compañía realiza funciones, intervenciones y talleres en espacios públicos, escuelas y centros culturales, promoviendo la creatividad y la participación familiar. Su labor integra investigación, diseño, confección, animación y gestión cultural, transmitiendo conocimientos y valores del oficio. OjoPiojo conecta la imaginación con la comunidad, construyendo encuentros que inspiran, educan y generan emociones compartidas, reafirmando el títere como patrimonio vivo y vehículo de transformación social.';
                break;
            case 'soquete-bestial-60-anos':
                $copy = 'Una fina selección de rutinas de una extensa trayectoria. En un ambiente lúdico, alegre y dinámico, este payaso nos llevará por diversas y divertidas rutinas, interactuando constantemente con el público asistente.';
                $secondaryCopy = 'Compañía compuesta por la familia Quiroga Beltrán.';
                break;
            case 'como-ser-una-gotita-de-agua':
                $copy = 'Gotita se golpeó en la cabeza y olvidó cómo ser una gotita de agua. En su búsqueda por recuperar su identidad, se enfrenta a personajes y situaciones inesperadas que la invitan a descubrir el valor de ser agua y a seguir su propio camino. Un viaje divertido y emotivo que atraviesa estanques, cañerías y nubes para volver al ciclo del agua.';
                $secondaryCopy = 'Miniteatros es una compañía dedicada al Teatro Lambe Lambe y al teatro de animación en pequeño formato, orientada a la creación, difusión y mediación de experiencias escénicas. Fundada en 2020, desarrolla espectáculos, talleres e instancias pedagógicas que acercan estas disciplinas a públicos diversos. A lo largo de su trayectoria ha participado en festivales nacionales e internacionales como Festilambe (Valparaíso, 2024), Lambe Insular (Chiloé, 2023–2024), Animaneco (Brasil, 2025), Festival Lluvia de Muñecos (2025) y Formanimada (2025). Asimismo, ha presentado su trabajo artístico y formativo en escuelas, bibliotecas, municipalidades, centros culturales y espacios comunitarios, contribuyendo a la difusión del teatro de animación en distintos territorios. Su repertorio incluye cuatro espectáculos de Teatro Lambe Lambe, entre ellos la trilogía Minimundos: Lágrimas (2020), Hielo Rojo (2025) y Lado Oscuro (2026), junto a La leyenda de Licarayén (2023). A ello se suma Cómo ser una gotita de agua (2025), obra de teatro de animación que combina narración oral y marionetas. Esta obra fue desarrollada durante una residencia artística en el Centro Cultural La Perrera en 2025.';
                break;
        }

        return [
            'tags'         => [$event['tag'] ?? '', $event['type'] ?? ''],
            'title'        => $event['title'] ?? lang('Billboard.default_title'),
            'image'        => $event['image'] ?? 'assets/img/menu/menu_programacion.webp',
            'company'      => $event['company'] ?? '',
            'audience'     => $event['audience'] ?? '',
            'date'         => $event['dateLabel'] ?? lang('Billboard.default_date'),
            'time'         => $event['timeLabel'] ?? lang('Billboard.default_time'),
            'priceGeneral' => 'General: $4.500',
            'priceReduced' => 'Estud. y 3ª Edad: $3.500',
            'copy'         => $copy,
            'secondaryCopy' => $secondaryCopy,
            'closingImage' => 'assets/img/billboard/billboard-collage.webp',
            'qrImage'      => 'assets/img/school/teatroescuela-qr.webp',
            'closingNote'  => lang('Billboard.default_closing_note'),
        ];
    }
}
