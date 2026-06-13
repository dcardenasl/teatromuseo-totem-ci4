/**
 * Language selector page
 */
(function() {
    const splashEl = document.querySelector('.language-layout');
    if (!splashEl) return;

    const targetUrl = splashEl.dataset.targetUrl || '/';

    function setLanguage(lang) {
        const secureFlag = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = 'totem_lang=' + lang + '; path=/; max-age=31536000; SameSite=Lax' + secureFlag;
        localStorage.setItem('totem_lang', lang);
        if (window.launchLanguageSelection) {
            window.launchLanguageSelection(targetUrl);
        } else {
            window.location.href = targetUrl;
        }
    }

    function closeLanguageSelection() {
        if (window.launchLanguageSelection) {
            window.launchLanguageSelection(targetUrl);
        } else {
            window.location.href = targetUrl;
        }
    }

    window.setLanguage = setLanguage;
    window.closeLanguageSelection = closeLanguageSelection;

    function initLanguageInteractions() {
        const buttons = document.querySelectorAll('.pill-button--language');
        const activeLang = (window.getActiveTotemLocale ? window.getActiveTotemLocale() : 'es');

        function syncActiveState(lang) {
            buttons.forEach((btn) => {
                const isActive = btn.getAttribute('data-lang') === lang;
                btn.classList.toggle('is-active', isActive);
                btn.classList.toggle('is-inactive', !isActive);
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            document.querySelectorAll('.language-instruction').forEach((instruction) => {
                instruction.classList.toggle('is-highlighted', instruction.getAttribute('data-lang') === lang);
            });
        }

        syncActiveState(activeLang);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanguageInteractions, { once: true });
    } else {
        initLanguageInteractions();
    }
})();
