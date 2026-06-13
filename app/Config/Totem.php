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
     * Constructor - permite sobreescribir con variables de entorno.
     */
    public function __construct()
    {
        parent::__construct();

        $this->enableTransitions = (bool) ($_ENV['TOTEM_ENABLE_TRANSITIONS'] ?? true);
        $this->enableAnimations  = (bool) ($_ENV['TOTEM_ENABLE_ANIMATIONS'] ?? true);
    }

    /**
     * Enable non-essential animations.
     */
    public bool $enableAnimations = true;
}
