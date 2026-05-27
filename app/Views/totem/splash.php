<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-page totem-page--center">
    <section class="screen splash-screen" aria-label="Pantalla de inicio">
        <div class="screen__content splash-scene">
            <div class="splash-sky" aria-hidden="true">
                <span class="splash-bird splash-bird--top"></span>
                <span class="splash-bird splash-bird--mid"></span>
                <span class="splash-bird splash-bird--low"></span>
                <span class="splash-flower splash-flower--left"></span>
                <span class="splash-flower splash-flower--right"></span>
            </div>

            <div class="splash-copy">
                <span class="splash-eyebrow">Descubre</span>
                <h1 class="splash-title">Teatromuseo</h1>
                <a class="hero__cta splash-cta" href="<?= base_url('language') ?>">Toca para comenzar</a>
            </div>

            <div class="splash-collage" aria-hidden="true">
                <span class="splash-cloud splash-cloud--left"></span>
                <span class="splash-cloud splash-cloud--right"></span>
                <span class="splash-stage splash-stage--performer"></span>
                <span class="splash-stage splash-stage--horn"></span>
                <span class="splash-stage splash-stage--house"></span>
                <span class="splash-stage splash-stage--tower"></span>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
