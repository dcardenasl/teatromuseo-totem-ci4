<?php

declare(strict_types=1);

namespace App\Repositories;

/**
 * Provides static fallback data for the Teatro Escuela screen.
 *
 * Used when the courses API is unavailable so the kiosk remains usable.
 */
final class SchoolFallbackRepository
{
    /**
     * @return list<array<string, mixed>>
     */
    public function courses(string $fallbackStart): array
    {
        return [
            [
                'tag'   => lang('Section.school_course_tag'),
                'title' => lang('Section.school_course_title'),
                'start' => $fallbackStart,
                'copy'  => lang('Section.school_course_copy'),
            ],
            [
                'tag'   => lang('Menu.audience_kids'),
                'title' => lang('Section.school_course_child_title'),
                'start' => $fallbackStart,
                'copy'  => lang('Section.school_course_child_copy'),
            ],
            [
                'tag'   => lang('Menu.audience_international'),
                'title' => lang('Section.school_course_mask_title'),
                'start' => $fallbackStart,
                'copy'  => lang('Section.school_course_mask_copy'),
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function teachers(): array
    {
        return [
            ['tone' => 'teacher-card--amber', 'name' => 'Marta Jara',   'role' => lang('Section.teacher_marta_role'),   'description' => lang('Section.teacher_marta_desc')],
            ['tone' => 'teacher-card--navy', 'name' => 'Tomás Vega',   'role' => lang('Section.teacher_tomas_role'),   'description' => lang('Section.teacher_tomas_desc')],
            ['tone' => 'teacher-card--olive', 'name' => 'Paula Montt',  'role' => lang('Section.teacher_paula_role'),  'description' => lang('Section.teacher_paula_desc')],
            ['tone' => 'teacher-card--sepia', 'name' => 'Nicolás Ríos', 'role' => lang('Section.teacher_nicolas_role'), 'description' => lang('Section.teacher_nicolas_desc')],
            ['tone' => 'teacher-card--rose', 'name' => 'Valentina Soto', 'role' => lang('Section.teacher_valentina_role'), 'description' => lang('Section.teacher_valentina_desc')],
            ['tone' => 'teacher-card--crimson', 'name' => 'Javier Lobo', 'role' => lang('Section.teacher_javier_role'), 'description' => lang('Section.teacher_javier_desc')],
            ['tone' => 'teacher-card--gold', 'name' => 'Camila Figueroa', 'role' => lang('Section.teacher_camila_role'), 'description' => lang('Section.teacher_camila_desc')],
            ['tone' => 'teacher-card--ink', 'name' => 'Rodrigo Salas', 'role' => lang('Section.teacher_rodrigo_role'), 'description' => lang('Section.teacher_rodrigo_desc')],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function students(): array
    {
        return [
            ['tone' => 'teacher-card--amber', 'name' => 'Lucía Paredes',  'role' => lang('Section.student_lucia_role'),    'description' => lang('Section.student_lucia_desc')],
            ['tone' => 'teacher-card--navy', 'name' => 'Diego Araya',    'role' => lang('Section.student_diego_role'),    'description' => lang('Section.student_diego_desc')],
            ['tone' => 'teacher-card--olive', 'name' => 'Sofía Núñez',    'role' => lang('Section.student_sofia_role'),    'description' => lang('Section.student_sofia_desc')],
            ['tone' => 'teacher-card--sepia', 'name' => 'Benjamín Toro',  'role' => lang('Section.student_benjamin_role'), 'description' => lang('Section.student_benjamin_desc')],
            ['tone' => 'teacher-card--rose', 'name' => 'Antonia Rojas',  'role' => lang('Section.student_antonia_role'),  'description' => lang('Section.student_antonia_desc')],
            ['tone' => 'teacher-card--crimson', 'name' => 'Matías Bravo', 'role' => lang('Section.student_matias_role'),  'description' => lang('Section.student_matias_desc')],
            ['tone' => 'teacher-card--gold', 'name' => 'Elena Cárdenas', 'role' => lang('Section.student_elena_role'),   'description' => lang('Section.student_elena_desc')],
            ['tone' => 'teacher-card--ink', 'name' => 'Felipe Muñoz',   'role' => lang('Section.student_felipe_role'),  'description' => lang('Section.student_felipe_desc')],
        ];
    }

    /**
     * Static section metadata for the school screen.
     *
     * @return array<string, mixed>
     */
    public function section(string $fallbackStart): array
    {
        return [
            'title'              => lang('Menu.school'),
            'heroImage'          => 'assets/img/menu/menu_escuela.webp',
            'heroAlt'            => lang('Section.school_hero_alt'),
            'introCopy'          => lang('Section.school_intro'),
            'stats'              => [
                ['label' => lang('Section.school_stat_courses'), 'value' => '50'],
                ['label' => lang('Section.school_stat_teachers'), 'value' => '20'],
                ['label' => lang('Section.school_stat_students'), 'value' => '1000'],
            ],
            'teachersTitle'      => lang('Section.school_teachers_title'),
            'studentsTitle'      => lang('Section.school_students_title'),
            'coursesTitle'       => lang('Section.school_courses_title'),
            'courseImage'        => 'assets/img/menu/menu_programacion.webp',
            'courseTag'          => lang('Section.school_course_tag'),
            'courseTitle'        => lang('Section.school_course_title'),
            'courseStart'        => $fallbackStart,
            'courseCopy'         => lang('Section.school_course_copy'),
            'courseContactLabel' => lang('Section.course_contact_label'),
            'courseContact'      => lang('Section.school_course_contact_value'),
            'courseQrLabel'      => lang('Section.school_course_qr_label'),
            'courseQrImage'      => 'assets/img/school/teatroescuela-qr.png',
            'courseQrUrl'        => 'https://teatromuseo.cl/teatro-escuela?utm_source=totem',
            'closingImage'       => 'assets/img/teatro-escuela/collage.webp',
            'logoPrimary'        => 'assets/img/logos/ministerio_culturas_chile.png',
            'logoSecondary'      => 'assets/img/menu/menu_escuela.webp',
        ];
    }
}
