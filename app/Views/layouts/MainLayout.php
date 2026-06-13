<?php
$cssPath = FCPATH . 'assets/css/style.css';
$cssVersion = file_exists($cssPath) ? filemtime($cssPath) : '1.0.0';
$jsPath = FCPATH . 'assets/js/app.js';
$jsVersion = file_exists($jsPath) ? filemtime($jsPath) : '1.0.0';
$totemConfig = config('Totem');
$transitionsEnabled = (bool) $totemConfig->enableTransitions;
$animationsEnabled = (bool) $totemConfig->enableAnimations;
$extraClasses = [];

if (! $transitionsEnabled) {
    $extraClasses[] = 'totem-transitions-disabled';
}

if (! $animationsEnabled) {
    $extraClasses[] = 'totem-animations-disabled';
}

$bodyClasses = trim(($bodyClass ?? 'totem-app') . ' ' . implode(' ', $extraClasses));

$systemMessages = [];
foreach (totem_locales() as $locale) {
    $code = $locale['code'];
    $systemMessages[$code] = [
        'rotateTitle'  => lang('Nav.rotate_title', [], $code),
        'rotateText'   => lang('Nav.rotate_text', [], $code),
        'idleMsg'      => lang('Idle.msg', [], $code),
        'idleContinue' => lang('Idle.continue', [], $code),
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= esc($htmlLang ?? 'es') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#de5928">
    <title><?= esc($pageTitle ?? 'Teatromuseo - Tótem Interactivo') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>?v=<?= $cssVersion ?>">
    <style>
        :root {
            --paper-texture: url('<?= base_url('assets/img/ui/texture.png') ?>');
        }
    </style>
    <script>
        window.TOTEM_SYSTEM_MESSAGES = <?= json_encode(
            $systemMessages,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?>;
        window.TOTEM_CONFIG = {
            enableTransitions: <?= $transitionsEnabled ? 'true' : 'false' ?>,
            enableAnimations: <?= $animationsEnabled ? 'true' : 'false' ?>
        };
    </script>
    <?= $this->renderSection('styles') ?>
</head>
<body class="<?= esc($bodyClasses) ?>">
    <div class="orientation-warning">
        <div class="orientation-warning__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                <line x1="12" y1="18" x2="12.01" y2="18"/>
            </svg>
        </div>
        <div class="orientation-warning__title"><?= lang('Nav.rotate_title') ?></div>
        <div class="orientation-warning__text"><?= lang('Nav.rotate_text') ?></div>
    </div>

    <div class="kiosk-shell">
        <main class="totem-stage">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Overlay de Transición Lúdica Multimodal -->
    <div id="totem-transition-overlay" class="totem-transition-overlay">
        <!-- 1. Nariz de Payaso -->
        <div class="totem-transition-overlay__nose"></div>
        
        <!-- 2. Telón de Teatro -->
        <div class="totem-transition-overlay__curtain-left"></div>
        <div class="totem-transition-overlay__curtain-right"></div>
        
        <!-- 3. Confeti y Serpentinas (Partículas directas en primer plano) -->
        <div class="totem-transition-overlay__confetti-container"></div>

        <!-- 4. Guante de Boxeo con Resorte Slapstick (SVGs para máxima fidelidad) -->
        <div class="totem-transition-overlay__glove-accordion">
            <svg viewBox="0 0 300 60" preserveAspectRatio="none" class="totem-transition-overlay__spring-svg">
                <path d="M0,30 L30,10 L60,50 L90,10 L120,50 L150,10 L180,50 L210,10 L240,50 L270,10 L300,30" fill="none" stroke="#ffe600" stroke-width="8" stroke-linejoin="round" stroke-linecap="round" />
            </svg>
        </div>
        <div class="totem-transition-overlay__glove">
            <svg viewBox="0 0 100 100" class="totem-transition-overlay__glove-svg">
                <!-- Guante Rojo -->
                <path d="M30,55 C12,45 8,62 18,72 C28,82 45,72 45,62" fill="#d60000" stroke="#333" stroke-width="4" stroke-linejoin="round" />
                <path d="M25,50 C25,25 42,10 65,10 C88,10 92,28 92,50 C92,72 82,85 62,85 C42,85 25,72 25,50 Z" fill="#ff2a2a" stroke="#333" stroke-width="4" stroke-linejoin="round" />
                <path d="M52,12 C58,28 58,68 52,83" stroke="#990000" stroke-width="3" fill="none" stroke-linecap="round" />
                <!-- Brillo -->
                <path d="M38,25 C50,18 62,18 70,24" stroke="#fff" stroke-width="5" stroke-linecap="round" fill="none" opacity="0.75" />
                <!-- Muñequera -->
                <rect x="52" y="72" width="22" height="15" rx="4" fill="#fff" stroke="#333" stroke-width="3.5" transform="rotate(-25 52 72)" />
            </svg>
        </div>
        <!-- Impacto de Cómic ¡PUM! -->
        <div class="totem-transition-overlay__glove-bam">
            <svg viewBox="0 0 200 200" class="totem-transition-overlay__bam-svg">
                <!-- Estrella de fondo amarilla con borde rojo grueso -->
                <polygon points="100,10 120,50 160,35 145,75 190,90 145,105 160,145 120,130 100,170 80,130 40,145 55,105 10,90 55,75 40,35 80,50" fill="#ffe600" stroke="#e60000" stroke-width="6" stroke-linejoin="round" />
                <!-- Estrella interna roja con borde amarillo delgado -->
                <polygon points="100,25 115,55 145,45 133,77 165,90 133,103 145,135 115,125 100,155 85,125 55,135 67,103 35,90 67,77 55,45 85,55" fill="#e60000" stroke="#ffe600" stroke-width="2" stroke-linejoin="round" />
                <!-- Texto en tipografía pesada e inclinada -->
                <text x="100" y="105" font-family="'Impact', 'Arial Black', sans-serif" font-size="34" font-weight="900" fill="#fff" stroke="#000" stroke-width="2.5" text-anchor="middle" transform="rotate(-10 100 105)">¡PUM!</text>
            </svg>
        </div>
        
        <!-- 5. Mueca Cómica de Payaso -->
        <div class="totem-transition-overlay__grimace">
            <div class="totem-transition-overlay__grimace-eye totem-transition-overlay__grimace-eye--left"></div>
            <div class="totem-transition-overlay__grimace-eye totem-transition-overlay__grimace-eye--right"></div>
            <div class="totem-transition-overlay__grimace-mouth"></div>
        </div>
    </div>

    <!-- Overlay de Advertencia por Inactividad (Idle Warning) -->
    <div id="idle-overlay" class="idle-overlay idle-overlay--hidden">
        <div class="idle-overlay__card">
            <p class="idle-overlay__msg"><?= lang('Idle.msg') ?></p>
            <span class="idle-overlay__count" id="idle-count">15</span>
            <button type="button" class="pill-button" onclick="resetTimer()"><?= lang('Idle.continue') ?></button>
        </div>
    </div>

    <script src="<?= base_url('assets/js/app.js') ?>?v=<?= $jsVersion ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
