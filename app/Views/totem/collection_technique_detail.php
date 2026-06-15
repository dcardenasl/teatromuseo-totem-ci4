<?php
/**
 * @var array<string, mixed> $technique
 * @var array<int, array{label:string, href:string, active?:bool, disabled?:bool}> $tabs
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="collection-detail collection-detail--technique">
            <?= view('totem/partials/collection_section_nav', ['tabs' => $tabs ?? []]) ?>

            <?= view('totem/partials/collection_detail_stage', [
                'eyebrow'      => lang('Collection.technique_detail_eyebrow'),
                'title'        => $technique['title'] ?? '',
                'subtitle'     => $technique['subtitle'] ?? '',
                'image'        => $technique['image'] ?? '',
                'imageAlt'     => $technique['title'] ?? '',
                'previousHref' => $technique['previousHref'] ?? 'museo/coleccion/titeres/tecnicas/titere-de-hilo',
                'nextHref'     => $technique['nextHref'] ?? 'museo/coleccion/titeres/tecnicas/titere-de-guante',
            ]) ?>

            <section class="collection-detail__body">
                <div class="collection-detail__copy">
                    <p class="collection-detail__lead"><?= esc($technique['description'] ?? '') ?></p>
                </div>

                <section class="collection-detail__related" aria-labelledby="collection-related-title">
                    <h3 class="collection-detail__related-title" id="collection-related-title">
                        <?= esc(lang('Collection.technique_related_title')) ?>
                    </h3>

                    <div class="collection-detail__related-grid">
                        <?php foreach (($technique['related'] ?? []) as $related): ?>
                            <a class="collection-detail__related-item" href="<?= esc(base_url($related['href'] ?? ''), 'attr') ?>">
                                <span class="collection-detail__related-image">
                                    <img src="<?= esc(base_url('assets/img/museo/coleccion/titeres/titere.webp'), 'attr') ?>" alt="" aria-hidden="true">
                                </span>
                                <span class="collection-detail__related-label"><?= esc($related['label'] ?? '') ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <div class="collection-detail__actions">
                    <a class="pill-button collection-detail__cta" href="<?= esc(base_url($technique['ctaHref'] ?? 'museo/coleccion/titeres/exhibicion'), 'attr') ?>">
                        <?= esc($technique['ctaLabel'] ?? lang('Collection.collection_exhibit')) ?>
                    </a>
                    <a class="pill-button pill-button--secondary collection-detail__cta" href="<?= esc(base_url('museo/coleccion/titeres/tecnicas'), 'attr') ?>">
                        <?= esc(lang('Collection.technique_detail_back')) ?>
                    </a>
                </div>
            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => $technique['pageTitle'] ?? lang('Collection.techniques_title'),
        'content' => $content,
        'nav' => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
