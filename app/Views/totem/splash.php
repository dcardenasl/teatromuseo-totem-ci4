<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-page totem-page--center">
    <section class="screen splash-screen" aria-label="<?= esc(lang('Splash.screen_label'), 'attr') ?>">
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
<?php $locales = totem_locales(); ?>
<script>
(() => {
    const eyebrowCopies = [
        <?php foreach ($locales as $locale): ?>
        "<?= esc(lang('Splash.discover', [], $locale['code']), 'js') ?>",
        <?php endforeach; ?>
    ];

    const ctaCopies = [
        <?php foreach ($locales as $locale): ?>
        "<?= esc(lang('Splash.touch_start', [], $locale['code']), 'js') ?>",
        <?php endforeach; ?>
    ];

    const locales = <?= json_encode(array_map(fn($l) => $l['code'], $locales)) ?>;
    const EXIT_DURATION = 700;
    const GAP_DURATION = 60;
    const ENTER_DURATION = 700;

    const clearSplashInterval = () => {
        if (window.totemSplashLanguageInterval) {
            clearInterval(window.totemSplashLanguageInterval);
            window.totemSplashLanguageInterval = null;
        }

        if (window.totemSplashLanguageExitTimeout) {
            clearTimeout(window.totemSplashLanguageExitTimeout);
            window.totemSplashLanguageExitTimeout = null;
        }

        if (window.totemSplashLanguageGapTimeout) {
            clearTimeout(window.totemSplashLanguageGapTimeout);
            window.totemSplashLanguageGapTimeout = null;
        }

        if (window.totemSplashLanguageEnterTimeout) {
            clearTimeout(window.totemSplashLanguageEnterTimeout);
            window.totemSplashLanguageEnterTimeout = null;
        }
    };

    const startSplashLanguageCycle = () => {
        if (!window.TOTEM_CONFIG || !window.TOTEM_CONFIG.enableAnimations) {
            clearSplashInterval();
            return;
        }

        const eyebrowCurrent = document.querySelector('.splash-eyebrow--current');
        const eyebrowNext = document.querySelector('.splash-eyebrow--next');
        const eyebrowSlot = document.querySelector('.splash-copy__slot--eyebrow');
        const ctaCurrent = document.querySelector('.splash-cta__text--current');
        const ctaNext = document.querySelector('.splash-cta__text--next');
        const ctaSlot = document.querySelector('.splash-copy__slot--cta');

        clearSplashInterval();

        if (!eyebrowCurrent || !eyebrowNext || !eyebrowSlot || !ctaCurrent || !ctaNext || !ctaSlot) {
            return;
        }

        const cookieMatch = document.cookie.match(/(?:^|;\s*)totem_lang=([^;]+)/);
        const activeLocale = (cookieMatch ? decodeURIComponent(cookieMatch[1]) : null)
            || localStorage.getItem('totem_lang')
            || 'es';
        let currentIndex = Math.max(locales.indexOf(activeLocale), 0);

        const rotateCopy = () => {
            const nextIndex = (currentIndex + 1) % eyebrowCopies.length;

            eyebrowSlot.classList.remove('splash-copy__slot--entering');
            ctaSlot.classList.remove('splash-copy__slot--entering');
            eyebrowSlot.classList.add('splash-copy__slot--exiting');
            ctaSlot.classList.add('splash-copy__slot--exiting');

            window.totemSplashLanguageExitTimeout = setTimeout(() => {
                eyebrowCurrent.textContent = eyebrowCopies[nextIndex];
                ctaCurrent.textContent = ctaCopies[nextIndex];
                eyebrowNext.textContent = '';
                ctaNext.textContent = '';
                eyebrowSlot.classList.remove('splash-copy__slot--exiting');
                ctaSlot.classList.remove('splash-copy__slot--exiting');
                eyebrowSlot.classList.add('splash-copy__slot--between');
                ctaSlot.classList.add('splash-copy__slot--between');

                window.totemSplashLanguageGapTimeout = setTimeout(() => {
                    eyebrowSlot.classList.remove('splash-copy__slot--between');
                    ctaSlot.classList.remove('splash-copy__slot--between');
                    eyebrowSlot.classList.add('splash-copy__slot--entering');
                    ctaSlot.classList.add('splash-copy__slot--entering');

                    window.totemSplashLanguageEnterTimeout = setTimeout(() => {
                        eyebrowSlot.classList.remove('splash-copy__slot--entering');
                        ctaSlot.classList.remove('splash-copy__slot--entering');
                        currentIndex = nextIndex;
                        window.totemSplashLanguageExitTimeout = null;
                        window.totemSplashLanguageGapTimeout = null;
                        window.totemSplashLanguageEnterTimeout = null;
                    }, ENTER_DURATION);
                }, GAP_DURATION);
            }, EXIT_DURATION);
        };

        window.totemSplashLanguageInterval = setInterval(() => {
            rotateCopy();
            }, 5400);
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
