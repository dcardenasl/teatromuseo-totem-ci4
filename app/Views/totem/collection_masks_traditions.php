<?php
/**
 * @var array<int, array{title:string, slug:string}> $traditions
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="collection-page collection-traditions-page">
            <div class="collection-traditions" aria-label="<?= esc(lang('Collection.masks_traditions_title'), 'attr') ?>">
                <?php foreach ($traditions as $tradition): ?>
                    <section class="collection-traditions__item">
                        <div class="collection-traditions__media">
                            <a href="<?= esc(base_url('museo/coleccion/mascaras/tradiciones/' . $tradition['slug']), 'attr') ?>">
                                <img
                                    src="<?= base_url('assets/img/museo/coleccion/mascaras/' . $tradition['slug'] . '.webp') ?>"
                                    alt="<?= esc($tradition['title'], 'attr') ?>"
                                    class="collection-traditions__image"
                                >
                            </a>
                        </div>
                        <?= view('totem/partials/collection_pill', [
                            'label' => $tradition['title'],
                            'href'  => 'museo/coleccion/mascaras/tradiciones/' . $tradition['slug'],
                        ]) ?>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.masks_traditions_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
