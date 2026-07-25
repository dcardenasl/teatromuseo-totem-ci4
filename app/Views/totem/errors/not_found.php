<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <section class="content-panel content-panel--soft" aria-label="<?= esc(lang('Common.error_404_label'), 'attr') ?>">
                <h2 class="content-panel__title"><?= esc(lang('Common.error_404_title')) ?></h2>
                <p class="content-panel__text"><?= esc(lang('Common.error_404_copy')) ?></p>
                <a class="pill-button" href="<?= base_url('menu') ?>"><?= esc(lang('Nav.home')) ?></a>
            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Common.error_404_title'),
        'content' => $content,
        'nav' => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
