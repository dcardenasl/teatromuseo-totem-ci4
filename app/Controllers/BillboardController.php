<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Presenters\BillboardPresenter;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Handles the billboard list and detail screens.
 */
final class BillboardController extends BaseTotemController
{
    public function billboard(): string
    {
        $locale = $this->request->getLocale();
        $result = $this->totemApi()->shows($locale);
        $context = (new BillboardPresenter())->presentList($result, $locale);

        return view('totem/billboard', array_merge(
            $this->pageMeta(lang('Menu.programming')),
            [
                'nav'          => $this->shellNav(),
                'unavailable'  => $context['state'] === 'unavailable',
                'stale'        => $context['state'] === 'stale',
                'months'       => $context['months'],
                'events'       => $context['events'],
                'titleClass'   => 'billboard-title',
                'titleWidth'   => '8.5ch',
                'footerVariant' => 'billboard',
            ]
        ));
    }

    /**
     * Renders the page shell immediately, never blocking on a network call.
     * `show(..., cacheOnly: true)` only ever reads the local cache: a hit
     * (fresh or stale, the common case since `TOTEM-BFF-10`'s warm-up keeps
     * listings hot) renders the real content right away, exactly as before.
     * A cache miss — expected on a slug's first-ever view, since per-slug
     * detail pages are deliberately excluded from the warm-up to avoid an
     * unbounded fan-out — renders a loading placeholder instead of blocking
     * the whole page on a cold BFF round-trip; `billboardDetailData()` does
     * the real (network) fetch and the browser injects the result once it
     * arrives. See `TOTEM-BFF-17`.
     */
    public function billboardDetail(?string $slug = null): string
    {
        if ($slug === null || $slug === '') {
            throw PageNotFoundException::forPageNotFound();
        }

        $locale = $this->request->getLocale();
        $result = $this->totemApi()->show($locale, $slug, cacheOnly: true);

        if ($result->state === 'unavailable') {
            return view('totem/billboard_detail', array_merge(
                $this->pageMeta(lang('Menu.billboard_detail')),
                [
                    'nav' => $this->shellNav(base_url('cartelera')),
                    'unavailable' => false,
                    'deferred' => true,
                    'dataUrl' => site_url("cartelera/detalle/{$slug}/data"),
                    'detail' => null,
                ]
            ));
        }

        $detail = (new BillboardPresenter())->presentDetail($result, $locale);
        if ($detail === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('totem/billboard_detail', array_merge(
            $this->pageMeta(lang('Menu.billboard_detail')),
            [
                'nav' => $this->shellNav(base_url('cartelera')),
                'unavailable' => false,
                'deferred' => false,
                'stale' => $result->state === 'stale',
                'detail' => $detail,
            ]
        ));
    }

    /**
     * The real (network-backed) fetch behind the loading placeholder
     * `billboardDetail()` renders on a cold cache. Returns pre-rendered HTML
     * for the same `billboard_detail_content` partial the synchronous path
     * uses, so the markup only lives once. A confirmed-nonexistent slug
     * cannot become a real HTTP 404 here — the shell already responded 200
     * — so it reports `state: not_found` and the browser renders the
     * "not found" copy inline instead of a server-level redirect.
     */
    public function billboardDetailData(?string $slug = null): ResponseInterface
    {
        if ($slug === null || $slug === '') {
            return $this->response->setStatusCode(404)->setJSON(['state' => 'not_found']);
        }

        $locale = $this->request->getLocale();
        $result = $this->totemApi()->show($locale, $slug);

        if ($result->state === 'unavailable') {
            return $this->response->setJSON([
                'state' => 'unavailable',
                'html' => view('totem/partials/content_unavailable'),
            ]);
        }

        $detail = (new BillboardPresenter())->presentDetail($result, $locale);
        if ($detail === null) {
            return $this->response->setJSON(['state' => 'not_found']);
        }

        return $this->response->setJSON([
            'state' => $result->state,
            'html' => view('totem/partials/billboard_detail_content', [
                'detail' => $detail,
                'stale' => $result->state === 'stale',
            ]),
        ]);
    }
}
