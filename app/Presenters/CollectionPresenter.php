<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Services\TotemApiResult;

/**
 * Transforms real BFF catalog data (collection items, techniques,
 * categories) into the collection screens' view context. Mirrors
 * `BillboardPresenter`/`SchoolPresenter`: no mock data, a screen with
 * nothing to show says so.
 */
final class CollectionPresenter
{
    /** @var list<string> */
    private const TONES = ['coral', 'sky', 'violet', 'wine'];

    /**
     * Puppets/masks/clowns exhibit cards: `{title, copy, href, image, tone}`.
     *
     * @return list<array<string, mixed>>
     */
    public function exhibitCards(TotemApiResult $result): array
    {
        $cards = [];
        foreach ($result->list() as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $cards[] = [
                'title' => is_string($item['name'] ?? null) ? $item['name'] : '',
                'copy' => is_string($item['summary'] ?? null) ? $item['summary'] : '',
                'href' => 'museo/coleccion/fichas/' . $this->slugOrId($item),
                'image' => is_string($item['cover_image']['url'] ?? null) ? $item['cover_image']['url'] : '',
                'tone' => self::TONES[$index % count(self::TONES)],
            ];
        }

        return $cards;
    }

    /**
     * Technique catalog cards: `{title, copy, href, image, tone}`. There is
     * no per-technique image field in the catalog domain, so cards fall back
     * to a shared illustrative asset rather than a specific invented photo.
     *
     * @return list<array<string, mixed>>
     */
    public function techniqueCards(TotemApiResult $result): array
    {
        $cards = [];
        foreach ($result->list() as $index => $technique) {
            if (!is_array($technique)) {
                continue;
            }

            $cards[] = [
                'title' => is_string($technique['name'] ?? null) ? $technique['name'] : '',
                'copy' => is_string($technique['summary'] ?? null) ? $technique['summary'] : '',
                'href' => 'museo/coleccion/titeres/tecnicas/' . (is_string($technique['slug'] ?? null) ? $technique['slug'] : ''),
                'image' => 'assets/img/museo/coleccion/titeres/titere.webp',
                'tone' => self::TONES[$index % count(self::TONES)],
            ];
        }

        return $cards;
    }

    /**
     * @return array<string, mixed>|null null when the source confirms this
     *     technique doesn't exist (a genuine 404, distinct from "unavailable").
     */
    public function techniqueDetail(TotemApiResult $techniqueResult, TotemApiResult $relatedItemsResult): ?array
    {
        if ($techniqueResult->data === null) {
            return null;
        }

        $technique = $techniqueResult->map();
        $title = is_string($technique['name'] ?? null) ? $technique['name'] : '';
        $slug = is_string($technique['slug'] ?? null) ? $technique['slug'] : '';

        $related = [];
        foreach ($relatedItemsResult->list() as $item) {
            if (!is_array($item) || count($related) >= 3) {
                continue;
            }
            $related[] = [
                'label' => is_string($item['name'] ?? null) ? $item['name'] : '',
                'href' => 'museo/coleccion/fichas/' . $this->slugOrId($item),
            ];
        }

        return [
            'pageTitle' => $title,
            'title' => $title,
            'subtitle' => '',
            'description' => is_string($technique['summary'] ?? null) ? $technique['summary'] : '',
            'image' => 'assets/img/museo/coleccion/titeres/titere.webp',
            'related' => $related,
            'ctaLabel' => $this->langStr('Collection.collection_exhibit'),
            'ctaHref' => 'museo/coleccion/titeres/exhibicion',
            'previousHref' => 'museo/coleccion/titeres/tecnicas',
            'nextHref' => 'museo/coleccion/titeres/tecnicas',
            'slug' => $slug,
        ];
    }

    /**
     * @return array<string, mixed>|null null when the source confirms this
     *     item doesn't exist.
     */
    public function itemDetail(TotemApiResult $result, TotemApiResult $relatedItemsResult): ?array
    {
        if ($result->data === null) {
            return null;
        }

        $item = $result->map();
        $id = $item['id'] ?? null;
        $title = is_string($item['name'] ?? null) ? $item['name'] : '';
        $categorySlug = is_string($item['category']['slug'] ?? null) ? $item['category']['slug'] : 'titeres';

        $techniques = is_array($item['techniques'] ?? null) ? $item['techniques'] : [];
        $firstTechnique = is_array($techniques[0] ?? null) ? $techniques[0] : null;

        $related = [];
        foreach ($relatedItemsResult->list() as $relatedItem) {
            if (!is_array($relatedItem) || count($related) >= 3) {
                continue;
            }
            if (isset($relatedItem['id']) && $id !== null && $relatedItem['id'] === $id) {
                continue;
            }
            $related[] = [
                'label' => is_string($relatedItem['name'] ?? null) ? $relatedItem['name'] : '',
                'href' => 'museo/coleccion/fichas/' . $this->slugOrId($relatedItem),
            ];
        }

        return [
            'slug' => $this->slugOrId($item),
            'pageTitle' => $title !== '' ? sprintf('%s — %s', $title, $this->langStr('Collection.item_detail_title')) : $this->langStr('Collection.item_detail_title'),
            'title' => $title,
            'subtitle' => is_string($item['summary'] ?? null) ? $item['summary'] : '',
            'description' => is_string($item['contenido'] ?? null) && $item['contenido'] !== ''
                ? $item['contenido']
                : (is_string($item['summary'] ?? null) ? $item['summary'] : ''),
            'technique' => $firstTechnique !== null && is_string($firstTechnique['name'] ?? null) ? $firstTechnique['name'] : '—',
            'techniqueHref' => $firstTechnique !== null && is_string($firstTechnique['slug'] ?? null)
                ? 'museo/coleccion/titeres/tecnicas/' . $firstTechnique['slug']
                : '#',
            'origin' => is_string($item['origin'] ?? null) && $item['origin'] !== '' ? $item['origin'] : '—',
            'measurements' => is_string($item['dimensions'] ?? null) && $item['dimensions'] !== '' ? $item['dimensions'] : '—',
            'year' => is_string($item['period'] ?? null) && $item['period'] !== '' ? $item['period'] : '—',
            'donatedBy' => is_string($item['donated_by'] ?? null) && $item['donated_by'] !== '' ? $item['donated_by'] : '—',
            'code' => is_string($item['inventory_code'] ?? null) && $item['inventory_code'] !== '' ? $item['inventory_code'] : '—',
            'image' => is_string($item['cover_image']['url'] ?? null) ? $item['cover_image']['url'] : '',
            'previousHref' => "museo/coleccion/{$categorySlug}/exhibicion",
            'nextHref' => "museo/coleccion/{$categorySlug}/exhibicion",
            'related' => $related,
            'categorySlug' => $categorySlug,
        ];
    }

    /**
     * `hasClowns`/`hasMasks` toggles for the collection landing screen,
     * sourced from real published-item counts per category instead of
     * fetching the whole collection just to check a boolean.
     *
     * @return array{hasClowns: bool, hasMasks: bool}
     */
    public function categoryAvailability(TotemApiResult $result): array
    {
        $hasClowns = false;
        $hasMasks = false;
        foreach ($result->list() as $category) {
            if (!is_array($category)) {
                continue;
            }
            $slug = is_string($category['slug'] ?? null) ? $category['slug'] : '';
            $count = (int) ($category['item_count'] ?? 0);
            if ($slug === 'payasos') {
                $hasClowns = $count > 0;
            }
            if ($slug === 'mascaras') {
                $hasMasks = $count > 0;
            }
        }

        return ['hasClowns' => $hasClowns, 'hasMasks' => $hasMasks];
    }

    /** @param array<string, mixed> $item */
    private function slugOrId(array $item): string
    {
        if (is_string($item['slug'] ?? null) && $item['slug'] !== '') {
            return $item['slug'];
        }

        return isset($item['id']) ? (string) $item['id'] : '';
    }

    /**
     * Resolves a translation line as a plain string, joining the plural-form
     * array shape `lang()` can return — self-contained so this class works
     * in plain unit tests that never bootstrap CI4's `title` helper.
     */
    private function langStr(string $key): string
    {
        $value = lang($key);

        return is_array($value) ? implode(' ', $value) : $value;
    }
}
