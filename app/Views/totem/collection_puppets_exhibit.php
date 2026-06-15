<?php
/**
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="collection-page">
            <section class="collection-hero" aria-labelledby="collection-puppets-exhibit-title">
                <div class="collection-hero__media">
                    <div class="collection-hero__frame">
                        <img src="<?= base_url('assets/img/museo/coleccion/titeres/titere.webp') ?>" alt="" loading="eager">
                    </div>
                </div>

                <div class="collection-hero__copy">
                    <p class="collection-hero__eyebrow"><?= esc(lang('Collection.hero_eyebrow')) ?></p>
                    <h2 class="collection-hero__title" id="collection-puppets-exhibit-title"><?= esc(lang('Collection.puppets_exhibit_title')) ?></h2>
                    <p class="collection-hero__intro"><?= esc(lang('Collection.section_copy')) ?></p>
                    <p class="collection-hero__note"><?= esc(lang('Collection.section_details_copy')) ?></p>

                    <div class="collection-hero__actions">
                        <a class="pill-button collection-hero__cta" href="<?= esc(base_url('museo/coleccion/titeres/tecnicas'), 'attr') ?>">
                            <?= esc(lang('Collection.collection_techniques')) ?>
                        </a>
                        <a class="pill-button pill-button--secondary collection-hero__cta" href="<?= esc(base_url('museo/coleccion'), 'attr') ?>">
                            <?= esc(lang('Nav.back')) ?>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.puppets_exhibit_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
