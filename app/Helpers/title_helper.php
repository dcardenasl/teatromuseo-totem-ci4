<?php

declare(strict_types=1);

/**
 * Helper para títulos seguros con whitelist de HTML.
 */

if (!function_exists('safe_title')) {
    /**
     * Escapa un título permitiendo solo etiquetas HTML específicas.
     *
     * Whitelist permitida: <br>, <br/>, <br />, <strong>, </strong>
     *
     * @param string $title El título a escapar
     * @return string El título escapado con etiquetas permitidas restauradas
     */
    function safe_title(string $title): string
    {
        // Primero escapamos todo el HTML
        $escaped = esc($title);

        // Luego restauramos las etiquetas permitidas
        return str_replace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;', '&lt;strong&gt;', '&lt;/strong&gt;'],
            ['<br>', '<br>', '<br>', '<strong>', '</strong>'],
            $escaped
        );
    }
}

if (!function_exists('media_url')) {
    /**
     * Resolves an image/media path for output, whichever source it came
     * from: a BFF-hydrated file URL (already absolute — `http(s)://…`) is
     * returned as-is, a local static asset path (`assets/img/…`) is run
     * through `base_url()` as usual. Wrapping an already-absolute URL in
     * `base_url()` would double-prefix it into a broken link.
     */
    function media_url(string $path): string
    {
        if ($path === '') {
            return '';
        }

        return preg_match('#^https?://#i', $path) === 1 ? $path : base_url($path);
    }
}

if (!function_exists('lang_str')) {
    /**
     * Resolves a translation line as a plain string. `lang()` can return
     * `list<string>` for lines with plural forms — this joins that case
     * instead of letting a caller either crash on `(string)` casting an
     * array or leak the wrong type into a string-typed context.
     */
    function lang_str(string $line): string
    {
        $value = lang($line);

        return is_array($value) ? implode(' ', $value) : $value;
    }
}
