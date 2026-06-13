<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Application-wide Tótem configuration.
 */
class Totem extends BaseConfig
{
    /**
     * Supported UI locales.
     *
     * @var list<string>
     */
    public array $supportedLocales = ['es', 'en', 'fr', 'pt'];

    /**
     * Base directory for public image assets.
     */
    public string $imageBasePath = 'assets/img/';

    /**
     * Default fallback locale.
     */
    public string $defaultLocale = 'es';

    /**
     * Enable the playful page transition overlay.
     */
    public bool $enableTransitions = true;

    /**
     * Enable non-essential animations.
     */
    public bool $enableAnimations = true;

    /**
     * Constructor - permite sobreescribir con variables de entorno.
     */
    public function __construct()
    {
        parent::__construct();

        $this->enableTransitions = $this->envBool('TOTEM_ENABLE_TRANSITIONS', true);
        $this->enableAnimations  = $this->envBool('TOTEM_ENABLE_ANIMATIONS', true);
        $this->enableFileCache   = $this->envBool('TOTEM_ENABLE_FILE_CACHE', true);

        $cacheTtl = getenv('TOTEM_CACHE_TTL_SECONDS');
        if ($cacheTtl !== false && is_numeric($cacheTtl)) {
            $this->cacheTtlSeconds = (int) $cacheTtl;
        }
    }

    /**
     * Helper para leer booleanos desde variables de entorno.
     */
    private function envBool(string $key, bool $default): bool
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        $value = strtolower(trim($value));

        return ! in_array($value, ['false', '0', 'off', 'no', ''], true);
    }

    /**
     * Enable file-based API caching for offline resilience.
     */
    public bool $enableFileCache = true;

    /**
     * Cache TTL in seconds for API responses.
     */
    public int $cacheTtlSeconds = 60;

    /**
     * Cache directory path (relative to WRITEPATH or absolute).
     */
    public string $cachePath = 'cache/totem/';
}
