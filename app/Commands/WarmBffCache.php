<?php

declare(strict_types=1);

namespace App\Commands;

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
 * Only calls the listing/facet methods every screen actually needs on cold
 * load — never per-slug detail methods, which are unbounded and would turn
 * this into an item-by-item fan-out (ADR-010 forbids that shape regardless
 * of whether it's client-parallel or just unbounded-sequential).
 */
final class WarmBffCache extends BaseCommand
{
    protected $group       = 'totem';
    protected $name        = 'totem:warm-cache';
    protected $description = 'Proactively refreshes the BFF fresh/stale cache for every kiosk screen, across all locales.';

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

        foreach ($locales as $locale) {
            $counts[$this->warm(fn (): TotemApiResult => $client->shows($locale), "shows/{$locale}")]++;
            $counts[$this->warm(fn (): TotemApiResult => $client->courses($locale), "courses/{$locale}")]++;
            $counts[$this->warm(fn (): TotemApiResult => $client->collectionItems($locale, category: 'titeres'), "collection-items/{$locale}/titeres")]++;
            $counts[$this->warm(fn (): TotemApiResult => $client->collectionItems($locale, category: 'mascaras'), "collection-items/{$locale}/mascaras")]++;
            $counts[$this->warm(fn (): TotemApiResult => $client->collectionItems($locale, category: 'payasos'), "collection-items/{$locale}/payasos")]++;
        }

        $counts[$this->warm(fn (): TotemApiResult => $client->techniques(withCounts: true), 'techniques')]++;
        $counts[$this->warm(fn (): TotemApiResult => $client->catalogCategories(withCounts: true), 'catalog-categories')]++;

        $line = sprintf(
            'fresh=%d stale=%d unavailable=%d',
            $counts['fresh'],
            $counts['stale'],
            $counts['unavailable'],
        );
        log_message('info', '[totem:warm-cache] ' . $line);
        CLI::write('Cache warm-up complete: ' . $line, 'green');
    }

    /**
     * Runs one warm-up call, reports it, and pauses before returning — the
     * pause lives here so every call site pays it uniformly.
     *
     * @return 'fresh'|'stale'|'unavailable'
     */
    private function warm(callable $call, string $label): string
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

        return $result->state;
    }
}
