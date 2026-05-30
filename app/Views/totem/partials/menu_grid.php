<div class="menu-grid">
    <?php foreach ($items as $item): ?>
        <?= view('totem/partials/card', [
            'title'    => $item['title'] ?? 'Sin título',
            'href'     => $item['href'] ?? '#',
            'class'    => $item['class'] ?? '',
            'artClass' => ''
        ]) ?>
    <?php endforeach; ?>
</div>

<div class="footer-ornament" aria-hidden="true">
    <span class="footer-cloud footer-cloud--left"></span>
    <span class="footer-cloud footer-cloud--right"></span>
    <span class="footer-stage footer-stage--house"></span>
    <span class="footer-stage footer-stage--tower"></span>
    <span class="footer-stage footer-stage--bird"></span>
    <span class="footer-stage footer-stage--flower"></span>
    <span class="footer-stage footer-stage--flower-secondary"></span>
</div>
