<?php
$variant = $variant ?? 'section';
?>
<footer class="footer-brand footer-brand--<?= esc($variant) ?>">
    <div class="footer-ornament footer-ornament--<?= esc($variant) ?>" aria-hidden="true">
        <span class="footer-cloud footer-cloud--left"></span>
        <span class="footer-cloud footer-cloud--right"></span>
        <span class="footer-stage footer-stage--house"></span>
        <span class="footer-stage footer-stage--tower"></span>
        <span class="footer-stage footer-stage--bird"></span>
        <span class="footer-stage footer-stage--flower"></span>
        <span class="footer-stage footer-stage--flower-secondary"></span>
    </div>

    <div class="footer-brand__logos" aria-label="Logos institucionales">
        <div class="footer-brand__logo footer-brand__logo--state">
            <img
                class="footer-brand__logo-image"
                src="<?= esc(base_url('assets/img/logos/ministerio_culturas_chile.png'), 'attr') ?>"
                alt="Ministerio de las Culturas, las Artes y el Patrimonio"
            >
        </div>

        <div class="footer-brand__logo footer-brand__logo--brand" aria-label="Teatromuseo">
            <span class="footer-brand__mark" aria-hidden="true"></span>
            <span class="footer-brand__label">TEATROMUSEO</span>
        </div>
    </div>
</footer>
