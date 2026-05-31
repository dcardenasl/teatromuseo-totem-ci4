<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <section class="menu-grid" aria-label="Secciones del museo">
            <?php foreach ($items as $item): ?>
                <?= view('totem/partials/card', [
                    'title'    => $item['title'] ?? 'Sin título',
                    'href'     => $item['href'] ?? '#',
                    'class'    => $item['class'] ?? '',
                    'artClass' => '',
                    'img'      => $item['img'] ?? ''
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
            const label = document.getElementById('menu-title');
            if (label) {
                const lines = <?= json_encode(explode(' ', $exploreLabel)) ?>;
                label.innerHTML = lines.map((line) => `<span class="menu-title__line">${line}</span>`).join('');
            }
        })();
        </script>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Menu.explore_museum'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
