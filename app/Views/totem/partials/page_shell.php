<?php
/**
 * Componente Shell de Página Estándar
 *
 * @param string $title (opcional)
 * @param array $nav (opcional)
 */

$nav = $nav ?? [];
$pageTitle = $title ?? '';
?>

<div class="totem-page-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>

    <?php if (!empty($pageTitle)): ?>
    <section class="menu-title">
        <h1 class="menu-title__heading" id="page-title">
            <?php 
                // Permitimos <br> pero escapamos el resto del contenido
                $safeTitle = str_replace(['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'], '<br>', esc($pageTitle));
                echo $safeTitle;
            ?>
        </h1>
    </section>
    <?php endif; ?>

    <main class="page-content">
        <?= $content ?? '' ?>
    </main>
</div>
