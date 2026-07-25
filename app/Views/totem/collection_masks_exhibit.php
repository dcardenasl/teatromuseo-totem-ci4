<?php
/**
 * @var array<int, array<string, mixed>> $items
 * @var array<int, array{label:string, href:string, active?:bool, disabled?:bool}> $tabs
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?php if (!empty($items)): ?>
            <?= view('totem/partials/collection_grid', [
                'title'     => lang('Collection.masks_exhibit_title'),
                'intro'     => lang('Collection.section_copy'),
                'gridClass' => 'collection-grid--exhibit',
                'items'     => $items,
                'tabs'      => $tabs,
            ]) ?>
        <?php else: ?>
            <div class="collection-page">
                <section class="collection-hero" aria-labelledby="collection-masks-exhibit-title">
                    <div class="collection-hero__media">
                        <div class="collection-hero__frame">
                            <img src="<?= base_url('assets/img/museo/coleccion/mascaras/mascara.webp') ?>" alt="" loading="eager">
                        </div>
                    </div>

                    <div class="collection-hero__copy">
                        <p class="collection-hero__eyebrow"><?= esc(lang('Collection.hero_eyebrow')) ?></p>
                        <h2 class="collection-hero__title" id="collection-masks-exhibit-title"><?= esc(lang('Collection.masks_exhibit_title')) ?></h2>
                        <p class="collection-hero__intro"><?= esc(lang('Collection.section_copy')) ?></p>
                        <p class="collection-hero__note"><?= esc(lang('Collection.section_details_copy')) ?></p>

                        <div class="collection-hero__actions">
                            <a class="pill-button collection-hero__cta" href="<?= esc(base_url('museo/coleccion/mascaras/tradiciones'), 'attr') ?>">
                                <?= esc(lang('Collection.collection_traditions')) ?>
                            </a>
                            <a class="pill-button pill-button--secondary collection-hero__cta" href="<?= esc(base_url('museo/coleccion'), 'attr') ?>">
                                <?= esc(lang('Nav.back')) ?>
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.masks_exhibit_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
