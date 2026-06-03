<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?= view('totem/partials/page_shell', [
    'title' => lang('MuseumInfo.building_title'),
    'content' => '<p>' . esc(lang('Common.building_mock')) . '</p>',
    'nav' => $nav ?? []
]) ?>
<?= $this->endSection() ?>
