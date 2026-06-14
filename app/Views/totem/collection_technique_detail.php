<?php
/**
 * @var array{title:string, description?:string} $technique
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <article>
            <?php if (isset($technique['description'])): ?>
                <p><?= esc($technique['description']) ?></p>
            <?php endif; ?>
        </article>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.technique_prefix') . ' ' . $technique['title'],
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
