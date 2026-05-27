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
                    <span class="pill-button__icon" aria-hidden="true"><?= esc($action['icon'] ?? '') ?></span>
                    <span><?= esc($action['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</header>
