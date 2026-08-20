<?php

declare(strict_types=1);

namespace App\Commands;

use App\Presenters\BillboardPresenter;
use App\Services\TotemApiResult;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;
use Config\Totem;

/**
 * Proactively refreshes the fresh/stale BFF cache so the kiosk doesn't rely
 * purely on a visitor's request to populate it. A kiosk sits idle for long
 * stretches (unlike `teatromuseo-web`'s organic continuous traffic), so
 * without this, the fresh TTL (60s) can lapse and the next visitor pays the
 * full round-trip — or worse, hits it exactly during a BFF outage before the
 * disk-backed stale entry has ever been written. Meant to run on a schedule
 * (see TASKS.md `TOTEM-BFF-10` for the crontab line), never on-demand from a
 * request path.
 *
 * Never fetches per-slug detail methods across an *unbounded* catalog —
 * ADR-010 forbids that shape regardless of whether it's client-parallel or
 * just unbounded-sequential fan-out. Two deliberate, still-bounded
 * exceptions:
 *
 * - Cartelera's own detail pages (`TOTEM-BFF-18`): the listing is already
 *   capped to the same 5 events the screen displays
 *   (`BillboardPresenter::presentList()`), so warming those 5 slugs' detail
 *   pages stays bounded.
 * - Catálogo's collection items (`TOTEM-BFF-19`): rather than one HTTP call
 *   per piece (which *would* be unbounded as the museum's catalog grows),
 *   each category is fetched once with the BFF's DETAIL field set
 *   (`BffTotemClient::collectionItemsDetailed()`) — everything every
 *   piece's own detail page needs, for the whole category, in one call —
 *   and every item's detail cache entry is seeded locally from that single
 *   response (`BffTotemClient::seedCollectionItemDetails()`). Técnicas get
 *   the same treatment for free: its listing and detail queries already
 *   project the identical columns, so `techniques()`'s response alone is
 *   enough to seed every technique's detail entry too
 *   (`seedTechniqueDetails()`) — this repo's most "fundamental" content
 *   (David's words) ends up fully warmed with a bounded number of calls
 *   regardless of catalog size, exactly like a static JSON dump would be,
 *   without actually needing one.
 *
 * TeatroEscuela has no detail screen to warm at all
 * (`BffTotemClient::course()` is unused dead capability, not a gap here).
 */
final class WarmBffCache extends BaseCommand
{
    protected $group       = 'totem';
    protected $name        = 'totem:warm-cache';
    protected $description = 'Proactively refreshes the BFF fresh/stale cache for every kiosk screen, across all locales.';

    /** @var list<string> */
    private const CATALOG_CATEGORIES = ['titeres', 'mascaras', 'payasos'];

    /** Fixed delay between calls, cheap insurance against the BFF's per-IP throttle. */
    private const DELAY_MICROSECONDS = 250_000;

    private int $delayMicroseconds = self::DELAY_MICROSECONDS;

    /** Test-only seam — production runs always use the real throttle-safe delay. */
    public function setDelayMicroseconds(int $microseconds): void
    {
        $this->delayMicroseconds = $microseconds;
    }

    public function run(array $params): void
    {
        $client  = Services::totemApi();
        $locales = (new Totem())->supportedLocales;
        $counts  = ['fresh' => 0, 'stale' => 0, 'unavailable' => 0];
        $itemsSeeded = 0;

        foreach ($locales as $locale) {
            $showsResult = $this->warm(fn (): TotemApiResult => $client->shows($locale), "shows/{$locale}");
            $counts[$showsResult->state]++;

            foreach ($this->featuredEventSlugs($showsResult, $locale) as $slug) {
                $detailResult = $this->warm(fn (): TotemApiResult => $client->show($locale, $slug), "shows/{$locale}/{$slug}");
                $counts[$detailResult->state]++;
            }

            $counts[$this->warm(fn (): TotemApiResult => $client->courses($locale), "courses/{$locale}")->state]++;

            foreach (self::CATALOG_CATEGORIES as $category) {
                $counts[$this->warm(fn (): TotemApiResult => $client->collectionItems($locale, category: $category), "collection-items/{$locale}/{$category}")->state]++;

                $detailedResult = $this->warm(fn (): TotemApiResult => $client->collectionItemsDetailed($locale, $category), "collection-items-detailed/{$locale}/{$category}");
                $counts[$detailedResult->state]++;
                if ($detailedResult->state !== 'unavailable') {
                    $itemsSeeded += $client->seedCollectionItemDetails($locale, $detailedResult->list());
                }
            }
        }

        $techniquesResult = $this->warm(fn (): TotemApiResult => $client->techniques(withCounts: true), 'techniques');
        $counts[$techniquesResult->state]++;
        if ($techniquesResult->state !== 'unavailable') {
            $itemsSeeded += $client->seedTechniqueDetails($techniquesResult->list());
        }

        $counts[$this->warm(fn (): TotemApiResult => $client->catalogCategories(withCounts: true), 'catalog-categories')->state]++;

        $line = sprintf(
            'fresh=%d stale=%d unavailable=%d, catalog detail entries seeded=%d',
            $counts['fresh'],
            $counts['stale'],
            $counts['unavailable'],
            $itemsSeeded,
        );
        log_message('info', '[totem:warm-cache] ' . $line);
        CLI::write('Cache warm-up complete: ' . $line, 'green');
    }

    /**
     * The same up-to-5 slugs `billboard.php` actually links to — reusing
     * the presenter's own selection (upcoming-first, filled from the most
     * recent past) instead of re-implementing that rule here.
     *
     * @return list<string>
     */
    private function featuredEventSlugs(TotemApiResult $showsResult, string $locale): array
    {
        $slugs = [];
        foreach ((new BillboardPresenter())->presentList($showsResult, $locale)['events'] as $event) {
            if (is_string($event['slug'] ?? null) && $event['slug'] !== '') {
                $slugs[] = $event['slug'];
            }
        }

        return $slugs;
    }

    /**
     * Runs one warm-up call, reports it, and pauses before returning — the
     * pause lives here so every call site pays it uniformly.
     */
    private function warm(callable $call, string $label): TotemApiResult
    {
        $result = $call();

        if ($result->state === 'unavailable') {
            CLI::write("  {$label}: unavailable", 'yellow');
        } else {
            CLI::write("  {$label}: {$result->state}");
        }

        // Strictly sequential — ADR-010 forbids curl_multi/parallelism anywhere
        // in this stack to compensate for hosting process limits. This delay
        // is deliberate spacing against the BFF's 60 req/60s per-IP throttle,
        // not a workaround for anything broken.
        usleep($this->delayMicroseconds);

        return $result;
    }
}
