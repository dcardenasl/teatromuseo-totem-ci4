<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <section class="menu-grid" aria-label="Secciones del museo">
            <?php foreach ($items as $item): ?>
                <?= view('totem/partials/card', [
                    'title'    => $item['title'] ?? 'Sin título',
                    'href'     => $item['href'] ?? '#',
                    'class'    => $item['class'] ?? '',
                    'artClass' => '' 
                ]) ?>
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
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => 'Explora el Museo', // Need a fallback or dynamic title
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
