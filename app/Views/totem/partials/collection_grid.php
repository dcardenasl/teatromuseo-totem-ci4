<?php
/**
 * @var string $title
 * @var string $intro
 * @var string $gridClass
 * @var array<int, array{title:string, href:string, image:string, copy?:string, tone?:string}> $items
 * @var array<int, array{label:string, href:string, active?:bool, disabled?:bool}> $tabs
 * @var string $footer
 */

$title = $title ?? '';
$intro = $intro ?? '';
$gridClass = trim('collection-grid ' . ($gridClass ?? ''));
$items = $items ?? [];
$tabs = $tabs ?? [];
$footer = $footer ?? '';
?>

<section class="collection-grid-layout">
    <header class="collection-grid-layout__header">
        <?php if ($intro !== ''): ?>
            <p class="collection-grid-layout__intro"><?= esc($intro) ?></p>
        <?php endif; ?>

        <?= view('totem/partials/collection_section_nav', ['tabs' => $tabs]) ?>
    </header>

    <div class="<?= esc($gridClass) ?>">
        <?php foreach ($items as $item): ?>
            <?php $tone = trim('collection-card--' . ($item['tone'] ?? 'coral')); ?>
            <a class="collection-card <?= esc($tone) ?>" href="<?= esc(base_url($item['href'] ?? '#'), 'attr') ?>">
                <div class="collection-card__media" aria-hidden="true">
                    <img
                        class="collection-card__image"
                        src="<?= esc(base_url($item['image'] ?? ''), 'attr') ?>"
                        alt=""
                        loading="lazy"
                    >
                </div>
                <div class="collection-card__body">
                    <h3 class="collection-card__title"><?= esc($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['copy'])): ?>
                        <p class="collection-card__copy"><?= esc($item['copy']) ?></p>
                    <?php endif; ?>
                    <span class="collection-card__cta"><?= esc(lang('Collection.card_cta')) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($footer !== ''): ?>
        <p class="collection-grid-layout__footer"><?= esc($footer) ?></p>
    <?php endif; ?>
</section>
