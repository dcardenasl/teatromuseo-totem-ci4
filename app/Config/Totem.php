<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Totem extends BaseConfig
{
    public bool $enableTransitions;

    public bool $enableAnimations;

    public function __construct()
    {
        parent::__construct();

        $this->enableTransitions = $this->envBool('TOTEM_ENABLE_TRANSITIONS', false);
        $this->enableAnimations = $this->envBool('TOTEM_ENABLE_ANIMATIONS', true);
    }

    private function envBool(string $key, bool $default): bool
    {
        $value = env($key);

        if ($value === null || $value === '') {
            return $default;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        return $filtered ?? $default;
    }
}
