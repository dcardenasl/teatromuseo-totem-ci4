<?php
$actions = $nav ?? [];
?>
<header class="totem-header">
    <div class="totem-brand">
        <span class="totem-brand__mark" aria-hidden="true"></span>
    </div>

    <?php if ($actions !== []): ?>
        <nav class="totem-header__actions" aria-label="Navegación principal">
            <?php foreach ($actions as $action): ?>
                <a class="<?= esc($action['class'] ?? 'pill-button') ?>" href="<?= esc($action['href']) ?>">
                    <span class="pill-button__icon" aria-hidden="true">
                        <?php if ($action['icon'] === '◌'): ?>
                            <svg viewBox="0 0 45 38" style="width: 1.6em; height: 1.6em; display: inline-block; vertical-align: -0.35em;">
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
                                    <use href="#bubble-shape" x="2" y="2" fill="none" stroke="white" stroke-width="2" stroke-linejoin="round" />
                                    <text x="15" y="12" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="10.5" font-weight="700" fill="white" text-anchor="middle" dominant-baseline="central">A</text>
                                </g>
                                
                                <!-- Front Bubble (文) -->
                                <g>
                                    <use href="#bubble-shape" x="17" y="12" fill="none" stroke="white" stroke-width="2" stroke-linejoin="round" />
                                    <text x="30" y="22" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="10.5" font-weight="700" fill="white" text-anchor="middle" dominant-baseline="central">文</text>
                                </g>
                            </svg>
                        <?php elseif ($action['icon'] === '←'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 1.15em; height: 1.15em; display: inline-block; vertical-align: -0.15em;">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                        <?php elseif ($action['icon'] === '⌂'): ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 1.15em; height: 1.15em; display: inline-block; vertical-align: -0.15em;">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        <?php else: ?>
                            <?= esc($action['icon'] ?? '') ?>
                        <?php endif; ?>
                    </span>
                    <span><?= esc($action['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</header>
