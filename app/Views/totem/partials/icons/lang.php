<?php
/**
 * Icono de selector de idioma (burbujas A/文)
 *
 * @param string $class Clases CSS adicionales
 */
?>
<svg viewBox="0 0 45 38" class="<?= esc($class ?? 'icon-lang') ?>" aria-hidden="true">
    <defs>
        <!-- Symmetrical bubble path with bottom-left tail -->
        <path id="bubble-shape" d="M 7,2 h 14 a 5,5 0 0 1 5,5 v 8 a 5,5 0 0 1 -5,5 h -8 l -4,4 l 1,-4 h -3 a 5,5 0 0 1 -5,-5 v -8 a 5,5 0 0 1 5,-5 z" />
        
        <!-- Mask to cut a clean gap in the back bubble -->
        <mask id="bubble-cutout">
            <rect x="0" y="0" width="45" height="38" fill="white" />
            <!-- The front bubble with a thick stroke cuts a transparent gap -->
            <use href="#bubble-shape" x="17" y="12" fill="black" stroke="black" stroke-width="4.5" stroke-linejoin="round" />
        </mask>
    </defs>
    
    <!-- Back Bubble (A) with mask applied -->
    <g mask="url(#bubble-cutout)">
        <use href="#bubble-shape" x="2" y="2" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
        <text x="15" y="12" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="10.5" font-weight="700" fill="currentColor" text-anchor="middle" dominant-baseline="central">A</text>
    </g>
    
    <!-- Front Bubble (文) -->
    <g>
        <use href="#bubble-shape" x="17" y="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
        <text x="30" y="22" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="10.5" font-weight="700" fill="currentColor" text-anchor="middle" dominant-baseline="central">文</text>
    </g>
</svg>