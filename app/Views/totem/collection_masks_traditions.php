<?php
/**
 * @var array<int, array{title:string, slug:string}> $traditions
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="collection-page">
            <?= view('totem/partials/menu_grid', [
                'items' => array_map(
                    static fn (array $tradition): array => [
                        'title' => $tradition['title'],
                        'href'  => base_url('museo/coleccion/mascaras/tradiciones/' . $tradition['slug']),
                    ],
                    $traditions
                ),
                'showCoda' => false,
            ]) ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.masks_traditions_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
