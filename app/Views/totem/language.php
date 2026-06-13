<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="language-layout">
            <section class="language-card language-card--bare language-card--panel language-card--floating" aria-label="<?= esc(lang('Menu.select_language'), 'attr') ?>">
                <div class="language-card__chrome">
                    <button class="language-card__close pill-button pill-button--ghost" type="button" aria-label="<?= esc(lang('Menu.close_selector'), 'attr') ?>" onclick="closeLanguageSelection()">
                        <span class="pill-button__icon" aria-hidden="true">×</span>
                    </button>
                </div>

                <!-- Contenedor de instrucciones en todos los idiomas para el usuario nativo -->
                <div class="language-instructions-container" aria-label="<?= esc(lang('Menu.select_language'), 'attr') ?>">
                    <?php foreach (totem_locales() as $locale): ?>
                        <div class="language-instruction lang-instruction--<?= esc($locale['code'], 'attr') ?>" data-lang="<?= esc($locale['code'], 'attr') ?>">
                            <span class="language-instruction__decorator"></span>
                            <span class="language-instruction__text"><?= lang('Menu.select_language', [], $locale['code']) ?></span>
                            <span class="language-instruction__decorator"></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="language-grid language-grid--spacious" role="list" aria-label="<?= esc(lang('Common.languages_label'), 'attr') ?>">
                    <?php foreach (totem_locales() as $locale): ?>
                        <button class="pill-button pill-button--language" type="button" data-lang="<?= esc($locale['code'], 'attr') ?>" aria-pressed="false" onclick="setLanguage('<?= esc($locale['code'], 'js') ?>')">
                            <?= esc($locale['label']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <script>
        (() => {
            const targetUrl = '<?= !empty($from) ? esc(base_url($from), 'js') : esc(base_url('menu'), 'js') ?>';

            function setLanguage(lang) {
                const secureFlag = location.protocol === 'https:' ? '; Secure' : '';
                document.cookie = "totem_lang=" + lang + "; path=/; max-age=31536000; SameSite=Lax" + secureFlag;
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

            const initLanguageInteractions = () => {
                const buttons = document.querySelectorAll('.pill-button--language');
                const activeLang = (window.getActiveTotemLocale ? window.getActiveTotemLocale() : 'es');

                const syncActiveState = (lang) => {
                    buttons.forEach((btn) => {
                        const isActive = btn.getAttribute('data-lang') === lang;
                        btn.classList.toggle('is-active', isActive);
                        btn.classList.toggle('is-inactive', !isActive);
                        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });

                    document.querySelectorAll('.language-instruction').forEach((instruction) => {
                        instruction.classList.toggle('is-highlighted', instruction.getAttribute('data-lang') === lang);
                    });
                };

                syncActiveState(activeLang);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLanguageInteractions, { once: true });
            } else {
                initLanguageInteractions();
            }
        })();
        </script>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => '',
        'content' => $content,
        'nav' => $nav ?? [],
        'chromeHidden' => true
    ]) ?>
<?= $this->endSection() ?>
