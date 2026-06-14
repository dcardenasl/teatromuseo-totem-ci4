<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Repositories\MuseumFallbackRepository;

/**
 * Builds the editorial context for the "museum today" screen.
 */
final class MuseumTodayPresenter
{
    private const MUSEUM_BLOCK_EXCERPT_CHARS = 180;
    private const MUSEUM_TODAY_MAX_BLOCKS    = 4;

    private MuseumFallbackRepository $fallback;

    public function __construct(?MuseumFallbackRepository $fallback = null)
    {
        $this->fallback = $fallback ?? new MuseumFallbackRepository();
    }

    /**
     * Build a resilient editorial context for the museum today screen.
     *
     * @param array<string, mixed> $museum
     * @return array<string, mixed>
     */
    public function present(array $museum): array
    {
        $page   = isset($museum['page']) && is_array($museum['page']) ? $museum['page'] : [];
        $blocks = isset($museum['blocks']) && is_array($museum['blocks'])
            ? $this->normalizeBlocks($museum['blocks'])
            : [];

        if ($blocks === []) {
            $blocks = $this->fallback->blocks();
        }

        $sectionTitle = is_string($page['title'] ?? null) && trim((string) $page['title']) !== ''
            ? trim((string) $page['title'])
            : lang('MuseumInfo.main_title');

        $primary   = $blocks[0];
        $secondary = array_slice($blocks, 1);

        return [
            'eyebrow'      => lang('MuseumInfo.today_eyebrow'),
            'intro'        => lang('MuseumInfo.today_intro'),
            'headline'     => lang('MuseumInfo.today_title'),
            'image'        => 'assets/img/museo/el-museo/collage-historia-actual.webp',
            'imageAlt'     => lang('MuseumInfo.today_image_alt'),
            'sectionTitle' => is_string($sectionTitle) ? $sectionTitle : '',
            'primary'      => $primary,
            'blocks'       => $secondary,
            'stats'        => [
                [
                    'label' => lang('MuseumInfo.today_stat_blocks'),
                    'value' => str_pad((string) count($blocks), 2, '0', STR_PAD_LEFT),
                ],
                [
                    'label' => lang('MuseumInfo.today_stat_section'),
                    'value' => is_string($sectionTitle) ? $sectionTitle : '',
                ],
                [
                    'label' => lang('MuseumInfo.today_stat_focus'),
                    'value' => (string) ($primary['title'] ?? (is_string($sectionTitle) ? $sectionTitle : '')),
                ],
            ],
            'actions' => [
                [
                    'label' => lang('MuseumInfo.today_cta_building'),
                    'href'  => base_url('museo/el-museo/historia'),
                ],
                [
                    'label' => lang('MuseumInfo.today_cta_institution'),
                    'href'  => base_url('museo/el-museo/iglesia'),
                ],
                [
                    'label' => lang('MuseumInfo.today_cta_back'),
                    'href'  => base_url('museo/el-museo'),
                ],
            ],
        ];
    }

    /**
     * @param list<array<string, mixed>>|array<array<string, mixed>> $blocks
     * @return list<array<string, mixed>>
     */
    private function normalizeBlocks(array $blocks): array
    {
        $normalized = [];

        foreach ($blocks as $index => $block) {
            if (!is_array($block)) {
                continue;
            }

            $title = trim((string) ($block['title'] ?? ''));
            $copy  = $this->excerptContent((string) ($block['content'] ?? ''));

            if ($title === '' && $copy === '') {
                continue;
            }

            $normalized[] = [
                'index'    => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'title'    => $title !== '' ? $title : lang('MuseumInfo.today_blocks_heading'),
                'copy'     => $copy !== '' ? $copy : lang('MuseumInfo.today_intro'),
                'fallback' => false,
            ];
        }

        return array_slice($normalized, 0, self::MUSEUM_TODAY_MAX_BLOCKS);
    }

    private function excerptContent(string $html, int $limit = self::MUSEUM_BLOCK_EXCERPT_CHARS): string
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
}
