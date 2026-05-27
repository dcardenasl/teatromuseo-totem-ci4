<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-page">
    <div class="menu-layout">
        <?= $this->include('totem/partials/topbar') ?>

        <section class="menu-title">
            <span class="menu-title__eyebrow">Recorre la colección viva</span>
            <h1 class="menu-title__heading menu-title__heading--compact" id="menu-title">
                <span class="menu-title__line">Explora el</span>
                <span class="menu-title__line">Museo</span>
            </h1>
        </section>

        <section class="menu-grid" aria-label="Secciones del museo">
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

<script>
(() => {
    const lang = localStorage.getItem('totem_lang') || 'es';
    const titles = {
        es: ['Explora el', 'Museo'],
        en: ['Explore the', 'Museum'],
        fr: ['Explorer le', 'Musée'],
        pt: ['Explorar o', 'Museu']
    };

    const label = document.getElementById('menu-title');
    if (label) {
        const lines = titles[lang] || titles.es;
        label.innerHTML = lines.map((line) => `<span class="menu-title__line">${line}</span>`).join('');
    }
})();
</script>
<?= $this->endSection() ?>
