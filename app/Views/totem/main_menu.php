<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-page">
    <div class="menu-layout">
        <?= $this->include('totem/partials/topbar') ?>

        <section class="menu-title">
            <h1 class="menu-title__heading" id="menu-title">
                <?php if (service('request')->getLocale() === 'en'): ?>
                    <span class="menu-title__line">Main</span>
                    <span class="menu-title__line">Menu</span>
                <?php else: ?>
                    <span class="menu-title__line">Menú</span>
                    <span class="menu-title__line">Principal</span>
                <?php endif; ?>
            </h1>
        </section>

        <section class="menu-grid" aria-label="Secciones principales">
            <?php foreach ($items as $item): ?>
                <a class="menu-card <?= esc($item['class']) ?>" href="<?= esc($item['href']) ?>">
                    <div class="menu-card__art" aria-hidden="true">
                        <span class="menu-card__art-core"></span>
                    </div>
                    <h2 class="menu-card__title"><?= esc($item['title']) ?></h2>
                </a>
            <?php endforeach; ?>
        </section>

        <div class="footer-ornament" aria-hidden="true">
            <span class="footer-cloud footer-cloud--left"></span>
            <span class="footer-cloud footer-cloud--right"></span>
            <span class="footer-stage footer-stage--house"></span>
            <span class="footer-stage footer-stage--tower"></span>
            <span class="footer-stage footer-stage--bird"></span>
            <span class="footer-stage footer-stage--flower"></span>
            <span class="footer-stage footer-stage--flower-secondary"></span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
