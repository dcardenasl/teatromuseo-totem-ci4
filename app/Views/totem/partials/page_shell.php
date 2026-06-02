<?php
/**
 * Componente Shell de Página Estándar
 *
 * @param string $title (opcional)
 * @param array $nav (opcional)
 * @param bool $chromeHidden (opcional)
 * @param string $footerVariant (opcional)
 * @param string $titleWidth (opcional)
 * @param string $titleClass (opcional)
 */

$nav = $nav ?? [];
$pageTitle = $title ?? '';
$chromeHidden = (bool)($chromeHidden ?? false);
$titleWidth = trim((string)($titleWidth ?? ''));
$titleClass = trim((string)($titleClass ?? ''));
?>

<div class="totem-page-shell">
    <?php if (!$chromeHidden): ?>
        <?= view('totem/partials/topbar', ['nav' => $nav]) ?>

        <?php if (!empty($pageTitle)): ?>
        <section class="menu-title<?= $titleClass !== '' ? ' ' . esc($titleClass) : '' ?>">
            <h1 class="menu-title__heading" id="page-title"<?= $titleWidth !== '' ? ' style="--page-title-max-width: ' . esc($titleWidth) . ';"' : '' ?>>
                <?php
                    // Permitimos <br> y <strong>, pero escapamos el resto del contenido
                    $safeTitle = str_replace(
                        ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;', '&lt;strong&gt;', '&lt;/strong&gt;'],
                        ['<br>', '<br>', '<br>', '<strong>', '</strong>'],
                        esc($pageTitle)
                    );
                    echo $safeTitle;
                ?>
            </h1>
        </section>
        <?php endif; ?>
    <?php endif; ?>

    <main class="page-content">
        <?= $content ?? '' ?>
    </main>

    <?php if (!$chromeHidden): ?>
        <?= view('totem/partials/page_footer', ['variant' => $footerVariant ?? 'section']) ?>
    <?php endif; ?>
</div>
