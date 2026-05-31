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
(() => {
    const eyebrowCopies = [
        "<?= esc(lang('Splash.discover', [], 'es'), 'js') ?>",
        "<?= esc(lang('Splash.discover', [], 'en'), 'js') ?>",
        "<?= esc(lang('Splash.discover', [], 'fr'), 'js') ?>",
        "<?= esc(lang('Splash.discover', [], 'pt'), 'js') ?>"
    ];

    const ctaCopies = [
        "<?= esc(lang('Splash.touch_start', [], 'es'), 'js') ?>",
        "<?= esc(lang('Splash.touch_start', [], 'en'), 'js') ?>",
        "<?= esc(lang('Splash.touch_start', [], 'fr'), 'js') ?>",
        "<?= esc(lang('Splash.touch_start', [], 'pt'), 'js') ?>"
    ];

    const locales = ['es', 'en', 'fr', 'pt'];

    const clearSplashInterval = () => {
        if (window.totemSplashLanguageInterval) {
            clearInterval(window.totemSplashLanguageInterval);
            window.totemSplashLanguageInterval = null;
        }
    };

    const startSplashLanguageCycle = () => {
        const eyebrowText = document.querySelector('.splash-eyebrow');
        const ctaText = document.querySelector('.splash-cta__text');

        clearSplashInterval();

        if (!eyebrowText || !ctaText) {
            return;
        }

        const cookieMatch = document.cookie.match(/(?:^|;\s*)totem_lang=([^;]+)/);
        const activeLocale = (cookieMatch ? decodeURIComponent(cookieMatch[1]) : null)
            || localStorage.getItem('totem_lang')
            || 'es';
        let currentIndex = Math.max(locales.indexOf(activeLocale), 0);

        window.totemSplashLanguageInterval = setInterval(() => {
            eyebrowText.classList.add('splash-eyebrow--hidden');
            ctaText.classList.add('splash-cta__text--hidden');

            setTimeout(() => {
                currentIndex = (currentIndex + 1) % eyebrowCopies.length;

                eyebrowText.textContent = eyebrowCopies[currentIndex];
                ctaText.textContent = ctaCopies[currentIndex];

                eyebrowText.classList.remove('splash-eyebrow--hidden');
                ctaText.classList.remove('splash-cta__text--hidden');
            }, 400);
        }, 4000);
    };

    window.totemSplashCleanup = clearSplashInterval;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startSplashLanguageCycle, { once: true });
    } else {
        startSplashLanguageCycle();
    }
})();
</script>
<?= $this->endSection() ?>
