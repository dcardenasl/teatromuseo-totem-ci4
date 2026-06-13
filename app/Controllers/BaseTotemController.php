<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\MenuBuilder;
use App\Services\NavBuilder;
use App\Services\TotemApiInterface;
use Config\Services;

/**
 * Base controller shared by all Tótem domain controllers.
 *
 * Provides common rendering helpers and dependency access without
 * embedding domain logic.
 */
abstract class BaseTotemController extends BaseController
{
    private ?TotemApiInterface $apiService = null;

    /**
     * Get the shared Tótem API service.
     */
    protected function totemApi(): TotemApiInterface
    {
        if ($this->apiService === null) {
            $this->apiService = Services::totemApi();
        }

        return $this->apiService;
    }

    /**
     * Build common page metadata.
     *
     * @return array<string, mixed>
     */
    protected function pageMeta(string $title): array
    {
        return [
            'pageTitle' => 'Teatromuseo - ' . $title,
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
     * @return array<string, mixed>
     */
    protected function menuItem(string $title, string $href, string $copy, string $class, string $img = ''): array
    {
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
