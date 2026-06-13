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
