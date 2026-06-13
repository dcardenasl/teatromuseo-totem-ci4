/**
 * Splash screen - language cycling animation
 */
(function() {
    const EXIT_DURATION = 700;
    const GAP_DURATION  = 60;
    const ENTER_DURATION = 700;

    // Get locale data from data attributes injected in the HTML
    const splashEl = document.querySelector('.splash-screen');
    if (!splashEl) return;

    const localesData = splashEl.dataset.locales;
    if (!localesData) return;

    const locales = JSON.parse(localesData);
    if (!Array.isArray(locales) || locales.length === 0) return;

    const eyebrowCopies = locales.map(l => l.discover);
    const ctaCopies     = locales.map(l => l.touchStart);
    const localeCodes   = locales.map(l => l.code);

    function clearSplashInterval() {
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
    }

    function startSplashLanguageCycle() {
        if (!window.TOTEM_CONFIG || !window.TOTEM_CONFIG.enableAnimations) {
            clearSplashInterval();
            return;
        }

        const eyebrowCurrent = document.querySelector('.splash-eyebrow--current');
        const eyebrowNext    = document.querySelector('.splash-eyebrow--next');
        const eyebrowSlot    = document.querySelector('.splash-copy__slot--eyebrow');
        const ctaCurrent     = document.querySelector('.splash-cta__text--current');
        const ctaNext        = document.querySelector('.splash-cta__text--next');
        const ctaSlot        = document.querySelector('.splash-copy__slot--cta');

        clearSplashInterval();

        if (!eyebrowCurrent || !eyebrowNext || !eyebrowSlot || !ctaCurrent || !ctaNext || !ctaSlot) {
            return;
        }

        const cookieMatch = document.cookie.match(/(?:^|;\s*)totem_lang=([^;]+)/);
        const activeLocale = (cookieMatch ? decodeURIComponent(cookieMatch[1]) : null)
            || localStorage.getItem('totem_lang')
            || 'es';
        let currentIndex = Math.max(localeCodes.indexOf(activeLocale), 0);

        function rotateCopy() {
            const nextIndex = (currentIndex + 1) % eyebrowCopies.length;

            eyebrowSlot.classList.remove('splash-copy__slot--entering');
            ctaSlot.classList.remove('splash-copy__slot--entering');
            eyebrowSlot.classList.add('splash-copy__slot--exiting');
            ctaSlot.classList.add('splash-copy__slot--exiting');

            window.totemSplashLanguageExitTimeout = setTimeout(() => {
                eyebrowCurrent.textContent = eyebrowCopies[nextIndex];
                ctaCurrent.textContent     = ctaCopies[nextIndex];
                eyebrowNext.textContent    = '';
                ctaNext.textContent        = '';
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
        }

        window.totemSplashLanguageInterval = setInterval(() => {
            rotateCopy();
        }, 5400);
    }

    // Register cleanup for SPA navigation
    window.totemSplashCleanup = clearSplashInterval;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startSplashLanguageCycle, { once: true });
    } else {
        startSplashLanguageCycle();
    }
})();
