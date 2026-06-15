/**
 * Language selector page
 *
 * Behavior:
 * - When selecting a language: navigate to data-on-select
 * - When closing (X button): navigate to data-on-cancel (back to origin via 'from' parameter)
 *
 * Usage in language.php:
 * - data-on-select: where to go after language selection
 * - data-on-cancel: where to go when user closes the selector
 */
(function() {
    const languageLayoutEl = document.querySelector('.language-layout');
    if (!languageLayoutEl) return;

    // Destinations: on-select (language chosen) vs on-cancel (close button)
    const onSelectUrl = languageLayoutEl.dataset.onSelect || '/menu';
    const onCancelUrl = languageLayoutEl.dataset.onCancel || '/menu';

    function setLanguage(lang) {
        const secureFlag = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = 'totem_lang=' + lang + '; path=/; max-age=31536000; SameSite=Lax' + secureFlag;
        localStorage.setItem('totem_lang', lang);

        // After selecting language, navigate to on-select destination
        if (window.launchLanguageSelection) {
            window.launchLanguageSelection(onSelectUrl);
        } else {
            window.location.href = onSelectUrl;
        }
    }

    function closeLanguageSelection() {
        // Close button: go back to origin (on-cancel destination)
        if (window.launchLanguageSelection) {
            window.launchLanguageSelection(onCancelUrl);
        } else {
            window.location.href = onCancelUrl;
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
