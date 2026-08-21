<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BffTotemClient;
use App\Services\MenuBuilder;
use App\Services\NavBuilder;
use Config\Services;

/**
 * Base controller shared by all Tótem domain controllers.
 *
 * Provides common rendering helpers and dependency access without
 * embedding domain logic.
 */
abstract class BaseTotemController extends BaseController
{
    private ?BffTotemClient $apiService = null;

    /**
     * Get the shared Tótem BFF client.
     */
    protected function totemApi(): BffTotemClient
    {
        if ($this->apiService === null) {
            $this->apiService = Services::totemApi();
        }

        return $this->apiService;
    }

    /**
     * Build common page metadata.
     *
     * @param string|list<string> $title
     * @return array<string, mixed>
     */
    protected function pageMeta(string|array $title): array
    {
        $title = is_string($title) ? $title : '';
        $this->response->setHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');

        return [
            'pageTitle' => 'Teatromuseo - ' . $title,
            'metaRobots' => 'noindex, nofollow, noarchive',
            'bodyClass' => 'totem-app',
            'htmlLang'  => $this->request->getLocale(),
        ];
    }

    /**
     * Build the shell navigation actions.
     *
     * @return list<array<string, mixed>>
     */
    protected function shellNav(?string $backHref = null): array
    {
        return (new NavBuilder())->build($backHref);
    }

    /**
     * Build a menu card item.
     *
     * @param string|list<string> $title
     * @param string|list<string> $copy
     * @return array<string, mixed>
     */
    protected function menuItem(string|array $title, string $href, string|array $copy, string $class, string $img = ''): array
    {
        $title = is_string($title) ? $title : '';
        $copy  = is_string($copy) ? $copy : '';

        return (new MenuBuilder())->item($title, $href, $copy, $class, $img);
    }

    /**
     * Render a Tótem view merging page metadata.
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data, ?string $title = null): string
    {
        $meta = $title !== null ? $this->pageMeta($title) : [];

        return view($view, array_merge($meta, $data));
    }
}
