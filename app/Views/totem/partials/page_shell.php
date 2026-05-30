<?php
/**
 * Componente Shell de Página Estándar
 *
 * @param string $title
 * @param array $nav (opcional)
 */

$nav = $nav ?? [];
$pageTitle = $title ?? '';
?>

<div class="totem-page-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>

    <?php if (!empty($pageTitle)): ?>
    <section class="menu-title">
        <h1 class="menu-title__heading">
            <?php 
                $parts = explode(' ', $pageTitle, 2);
                foreach ($parts as $part): ?>
                <span class="menu-title__line"><?= esc($part) ?></span>
            <?php endforeach; ?>
        </h1>
    </section>
    <?php endif; ?>

    <main class="page-content">
        <?= $content ?? '' ?>
    </main>
</div>
