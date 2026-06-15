<?php
/**
 * @var string $title
 * @var array<string, mixed> $item
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="collection-detail">
            <?= view('totem/partials/collection_detail_stage', [
                'eyebrow'     => lang('Collection.item_detail_eyebrow'),
                'title'       => $item['title'] ?? '',
                'subtitle'    => $item['subtitle'] ?? '',
                'image'       => $item['image'] ?? '',
                'imageAlt'    => $item['title'] ?? '',
                'previousHref'=> $item['previousHref'] ?? '',
                'nextHref'    => $item['nextHref'] ?? '',
            ]) ?>

            <section class="collection-detail__body">
                <div class="collection-detail__copy">
                    <p class="collection-detail__lead"><?= esc($item['description'] ?? '') ?></p>
                </div>

                <dl class="collection-detail__facts">
                    <div class="collection-detail__fact">
                        <dt><?= esc(lang('Collection.item_meta_technique')) ?></dt>
                        <dd>
                            <a href="<?= esc(base_url($item['techniqueHref'] ?? ''), 'attr') ?>">
                                <?= esc($item['technique'] ?? '') ?>
                            </a>
                        </dd>
                    </div>
                    <div class="collection-detail__fact">
                        <dt><?= esc(lang('Collection.item_meta_origin')) ?></dt>
                        <dd><?= esc($item['origin'] ?? '') ?></dd>
                    </div>
                    <div class="collection-detail__fact">
                        <dt><?= esc(lang('Collection.item_meta_measurements')) ?></dt>
                        <dd><?= esc($item['measurements'] ?? '') ?></dd>
                    </div>
                    <div class="collection-detail__fact">
                        <dt><?= esc(lang('Collection.item_meta_year')) ?></dt>
                        <dd><?= esc($item['year'] ?? '') ?></dd>
                    </div>
                    <div class="collection-detail__fact">
                        <dt><?= esc(lang('Collection.item_meta_donated_by')) ?></dt>
                        <dd><?= esc($item['donatedBy'] ?? '') ?></dd>
                    </div>
                    <div class="collection-detail__fact">
                        <dt><?= esc(lang('Collection.item_meta_code')) ?></dt>
                        <dd><?= esc($item['code'] ?? '') ?></dd>
                    </div>
                </dl>

                <div class="collection-detail__actions">
                    <a class="pill-button collection-detail__cta" href="<?= esc(base_url($item['techniqueHref'] ?? ''), 'attr') ?>">
                        <?= esc(lang('Collection.item_cta_technique')) ?>
                    </a>
                    <a class="pill-button pill-button--secondary collection-detail__cta" href="<?= esc(base_url('museo/coleccion/titeres/exhibicion'), 'attr') ?>">
                        <?= esc(lang('Collection.item_cta_back')) ?>
                    </a>
                </div>
            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => $title ?? lang('Collection.puppets'),
        'content' => $content,
        'nav' => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
