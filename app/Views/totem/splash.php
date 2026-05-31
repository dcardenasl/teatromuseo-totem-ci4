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
                <span class="splash-eyebrow"><?= lang('Splash.discover') ?></span>
                <h1 class="splash-title">Teatromuseo</h1>
                <a class="hero__cta splash-cta" href="<?= base_url('language') ?>">
                    <span class="splash-cta__text"><?= lang('Splash.touch_start') ?></span>
                </a>
            </div>

            <div class="splash-collage" aria-hidden="true">
                <img src="<?= base_url('assets/img/menu/collage_referencia.webp') ?>" alt="Teatromuseo Collage" class="splash-collage__img">
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const eyebrowText = document.querySelector('.splash-eyebrow');
    const ctaText = document.querySelector('.splash-cta__text');
    
    if (eyebrowText && ctaText) {
        const eyebrows = [
            "<?= esc(lang('Splash.discover', [], 'es'), 'js') ?>",
            "<?= esc(lang('Splash.discover', [], 'en'), 'js') ?>",
            "<?= esc(lang('Splash.discover', [], 'fr'), 'js') ?>",
            "<?= esc(lang('Splash.discover', [], 'pt'), 'js') ?>"
        ];
        
        const ctas = [
            "<?= esc(lang('Splash.touch_start', [], 'es'), 'js') ?>",
            "<?= esc(lang('Splash.touch_start', [], 'en'), 'js') ?>",
            "<?= esc(lang('Splash.touch_start', [], 'fr'), 'js') ?>",
            "<?= esc(lang('Splash.touch_start', [], 'pt'), 'js') ?>"
        ];
        
        let currentIndex = 0;

        setInterval(() => {
            // Fade out both eyebrow (shifts up) and button text (shifts down)
            eyebrowText.classList.add('splash-eyebrow--hidden');
            ctaText.classList.add('splash-cta__text--hidden');
            
            // Wait for transition, then update content and fade-in/slide back
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % eyebrows.length;
                
                eyebrowText.textContent = eyebrows[currentIndex];
                ctaText.textContent = ctas[currentIndex];
                
                eyebrowText.classList.remove('splash-eyebrow--hidden');
                ctaText.classList.remove('splash-cta__text--hidden');
            }, 400);
        }, 4000);
    }
});
</script>
<?= $this->endSection() ?>
