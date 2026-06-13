<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<?php
$locales = totem_locales();
$localesData = array_map(fn($l) => [
    'code'        => $l['code'],
    'discover'    => lang('Splash.discover', [], $l['code']),
    'touchStart'  => lang('Splash.touch_start', [], $l['code']),
], $locales);
?>
<div class="totem-page totem-page--center">
    <section class="screen splash-screen" aria-label="<?= esc(lang('Splash.screen_label'), 'attr') ?>" data-locales="<?= esc(json_encode($localesData), 'attr') ?>">
        <div class="screen__content splash-scene">
            <div class="splash-sky" aria-hidden="true">
                <span class="splash-bird splash-bird--top"></span>
                <span class="splash-bird splash-bird--mid"></span>
                <span class="splash-bird splash-bird--low"></span>
                <span class="splash-flower splash-flower--left"></span>
                <span class="splash-flower splash-flower--right"></span>
            </div>

            <div class="splash-copy">
                <span class="splash-copy__slot splash-copy__slot--eyebrow">
                    <span class="splash-eyebrow splash-eyebrow--current"><?= lang('Splash.discover') ?></span>
                    <span class="splash-eyebrow splash-eyebrow--next" aria-hidden="true"></span>
                </span>
                <h1 class="splash-title">Teatromuseo</h1>
                <a class="hero__cta splash-cta" href="<?= base_url('language') ?>">
                    <span class="splash-copy__slot splash-copy__slot--cta">
                        <span class="splash-cta__text splash-cta__text--current"><?= lang('Splash.touch_start') ?></span>
                        <span class="splash-cta__text splash-cta__text--next" aria-hidden="true"></span>
                    </span>
                </a>
            </div>

            <div class="splash-collage" aria-hidden="true">
                <img src="<?= base_url('assets/img/menu/collage_referencia.webp') ?>" alt="<?= esc(lang('Splash.collage_alt'), 'attr') ?>" class="splash-collage__img">
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/splash.js') ?>"></script>
<?= $this->endSection() ?>
