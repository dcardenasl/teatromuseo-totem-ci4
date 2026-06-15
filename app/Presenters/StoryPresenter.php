<?php

declare(strict_types=1);

namespace App\Presenters;

/**
 * Builds the shared story payload for museum and history screens.
 */
final class StoryPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function museumBuilding(): array
    {
        return [
            'eyebrow' => lang('MuseumInfo.main_eyebrow'),
            'title' => lang('MuseumInfo.teatromuseo_history_title'),
            'hero' => [
                'frame' => 'assets/img/museo/el-museo/marco.webp',
                'items' => [
                    $this->imageItem('assets/img/museo/el-museo/collage-nuestra-historia.webp', lang('MuseumInfo.teatromuseo_history_alt')),
                ],
            ],
            'intro' => lang('MuseumInfo.main_copy'),
            'sections' => [
                [
                    'title' => lang('MuseumInfo.mission_title'),
                    'copy' => lang('MuseumInfo.mission_copy'),
                ],
                [
                    'title' => lang('MuseumInfo.vision_title'),
                    'copy' => lang('MuseumInfo.vision_copy'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function museumInstitution(): array
    {
        return [
            'eyebrow' => lang('MuseumInfo.church_eyebrow'),
            'title' => lang('MuseumInfo.church_history_title'),
            'hero' => [
                'frame' => 'assets/img/museo/el-museo/marco.webp',
                'items' => [
                    $this->imageItem('assets/img/museo/el-museo/collage-san-judas.webp', lang('MuseumInfo.church_history_alt')),
                ],
            ],
            'intro' => lang('MuseumInfo.church_history_intro'),
            'sections' => [
                [
                    'title' => lang('MuseumInfo.resistance_title'),
                    'copy' => lang('MuseumInfo.resistance_copy'),
                ],
                [
                    'title' => lang('MuseumInfo.institution_title'),
                    'copy' => lang('MuseumInfo.church_history_intro'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function museumToday(): array
    {
        return [
            'eyebrow' => lang('MuseumInfo.today_eyebrow'),
            'title' => lang('MuseumInfo.teatromuseo_today'),
            'hero' => [
                'frame' => 'assets/img/museo/el-museo/marco.webp',
                'items' => [
                    $this->imageItem('assets/img/museo/el-museo/collage-historia-actual.webp', lang('MuseumInfo.today_image_alt')),
                    $this->imageItem('assets/img/museo/el-museo/collage-nuestra-historia.webp', lang('MuseumInfo.teatromuseo_history_alt')),
                    $this->imageItem('assets/img/museo/el-museo/collage-san-judas.webp', lang('MuseumInfo.church_history_alt')),
                ],
            ],
            'intro' => lang('MuseumInfo.today_intro'),
            'sections' => [
                [
                    'title' => lang('MuseumInfo.today_story_title_1'),
                    'copy' => lang('MuseumInfo.today_story_copy_1'),
                ],
                [
                    'title' => lang('MuseumInfo.today_story_title_2'),
                    'copy' => lang('MuseumInfo.today_story_copy_2'),
                ],
                [
                    'title' => lang('MuseumInfo.today_story_title_3'),
                    'copy' => lang('MuseumInfo.today_story_copy_3'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function historyPost(string $slug): array
    {
        $posts = [
            'historia-del-circo' => [
                'eyebrow' => lang('ComicHistory.section_eyebrow'),
                'title' => lang('ComicHistory.entry_circus_history'),
                'intro' => lang('ComicHistory.entry_circus_intro'),
                'hero' => [
                    'frame' => 'assets/img/museo/el-museo/marco.webp',
                    'items' => [
                        $this->imageItem('assets/img/museo/historia/collage-circo.webp', lang('ComicHistory.entry_circus_history')),
                    ],
                ],
                'sections' => [
                    [
                        'title' => lang('ComicHistory.entry_circus_section_title_1'),
                        'copy' => lang('ComicHistory.entry_circus_section_copy_1'),
                    ],
                    [
                        'title' => lang('ComicHistory.entry_circus_section_title_2'),
                        'copy' => lang('ComicHistory.entry_circus_section_copy_2'),
                    ],
                ],
            ],
            'historia-de-los-payasos' => [
                'eyebrow' => lang('ComicHistory.section_eyebrow'),
                'title' => lang('ComicHistory.entry_clowns_history'),
                'intro' => lang('ComicHistory.entry_clowns_intro'),
                'hero' => [
                    'frame' => 'assets/img/museo/el-museo/marco.webp',
                    'items' => [
                        $this->imageItem('assets/img/museo/historia/collage-teatro.webp', lang('ComicHistory.entry_clowns_history')),
                    ],
                ],
                'sections' => [
                    [
                        'title' => lang('ComicHistory.entry_clowns_section_title_1'),
                        'copy' => lang('ComicHistory.entry_clowns_section_copy_1'),
                    ],
                    [
                        'title' => lang('ComicHistory.entry_clowns_section_title_2'),
                        'copy' => lang('ComicHistory.entry_clowns_section_copy_2'),
                    ],
                ],
            ],
            'tradicion-del-titere' => [
                'eyebrow' => lang('ComicHistory.section_eyebrow'),
                'title' => lang('ComicHistory.entry_puppetry_tradition'),
                'intro' => lang('ComicHistory.entry_puppetry_intro'),
                'hero' => [
                    'frame' => 'assets/img/museo/el-museo/marco.webp',
                    'items' => [
                        $this->imageItem('assets/img/museo/historia/historia-editorial.webp', lang('ComicHistory.entry_puppetry_tradition')),
                    ],
                ],
                'sections' => [
                    [
                        'title' => lang('ComicHistory.entry_puppetry_section_title_1'),
                        'copy' => lang('ComicHistory.entry_puppetry_section_copy_1'),
                    ],
                    [
                        'title' => lang('ComicHistory.entry_puppetry_section_title_2'),
                        'copy' => lang('ComicHistory.entry_puppetry_section_copy_2'),
                    ],
                ],
            ],
        ];

        if (isset($posts[$slug])) {
            return $posts[$slug];
        }

        return [
            'eyebrow' => lang('ComicHistory.section_eyebrow'),
            'title' => lang('ComicHistory.post_title'),
            'intro' => sprintf(lang('ComicHistory.chapter_placeholder'), 1),
            'hero' => [
                'frame' => 'assets/img/museo/el-museo/marco.webp',
                'items' => [
                    $this->imageItem('assets/img/museo/historia/historia-editorial.webp', lang('ComicHistory.post_title')),
                ],
            ],
            'sections' => [
                [
                    'title' => lang('ComicHistory.details_title'),
                    'copy' => lang('ComicHistory.details_copy'),
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function imageItem(string $src, string $alt): array
    {
        return [
            'type' => 'image',
            'src' => $src,
            'alt' => $alt,
        ];
    }
}
