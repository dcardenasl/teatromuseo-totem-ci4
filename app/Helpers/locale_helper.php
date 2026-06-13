<?php

declare(strict_types=1);

/**
 * Helper para locales del tótem.
 */

if (!function_exists('totem_locales')) {
    /**
     * Devuelve los locales soportados con sus etiquetas localizadas.
     *
     * @return list<array{code: string, label: string}>
     */
    function totem_locales(): array
    {
        /** @var Config\Totem|null $config */
        $config = config('Totem');

        if ($config === null) {
            return [];
        }

        $locales = [];

        foreach ($config->supportedLocales as $code) {
            $label = lang('Common.locale_' . $code, [], $code);
            $locales[] = [
                'code'  => $code,
                'label' => is_string($label) ? $label : $code,
            ];
        }

        return $locales;
    }
}

if (!function_exists('totem_locale_codes')) {
    /**
     * Devuelve solo los códigos de locale soportados.
     *
     * @return list<string>
     */
    function totem_locale_codes(): array
    {
        /** @var Config\Totem|null $config */
        $config = config('Totem');

        if ($config === null) {
            return [];
        }

        return $config->supportedLocales;
    }
}
