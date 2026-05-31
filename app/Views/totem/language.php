<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <style>
            .pill-button--language {
                min-width: 250px;
                padding: 1.5rem 2rem;
                font-size: 1.2rem;
            }
        </style>
        <section class="language-card language-card--bare">
            <!-- Contenedor de instrucciones en todos los idiomas para el usuario nativo -->
            <div class="language-instructions-container" aria-label="Selecciona tu idioma / Select your language / Sélectionnez votre langue / Selecione o seu idioma">
                <div class="language-instruction lang-instruction--es" data-lang="es">
                    <span class="language-instruction__decorator"></span>
                    <span class="language-instruction__text"><?= lang('Menu.select_language', [], 'es') ?></span>
                    <span class="language-instruction__decorator"></span>
                </div>
                <div class="language-instruction lang-instruction--en" data-lang="en">
                    <span class="language-instruction__decorator"></span>
                    <span class="language-instruction__text"><?= lang('Menu.select_language', [], 'en') ?></span>
                    <span class="language-instruction__decorator"></span>
                </div>
                <div class="language-instruction lang-instruction--fr" data-lang="fr">
                    <span class="language-instruction__decorator"></span>
                    <span class="language-instruction__text"><?= lang('Menu.select_language', [], 'fr') ?></span>
                    <span class="language-instruction__decorator"></span>
                </div>
                <div class="language-instruction lang-instruction--pt" data-lang="pt">
                    <span class="language-instruction__decorator"></span>
                    <span class="language-instruction__text"><?= lang('Menu.select_language', [], 'pt') ?></span>
                    <span class="language-instruction__decorator"></span>
                </div>
            </div>

            <div class="language-grid language-grid--spacious" role="list" aria-label="Idiomas">
                <button class="pill-button pill-button--language" type="button" data-lang="es" onclick="setLanguage('es')">
                    Español
                </button>
                <button class="pill-button pill-button--language" type="button" data-lang="en" onclick="setLanguage('en')">
                    English
                </button>
                <button class="pill-button pill-button--language" type="button" data-lang="fr" onclick="setLanguage('fr')">
                    Français
                </button>
                <button class="pill-button pill-button--language" type="button" data-lang="pt" onclick="setLanguage('pt')">
                    Português
                </button>
            </div>
        </section>

        <script>
        (() => {
            function setLanguage(lang) {
                document.cookie = "totem_lang=" + lang + "; path=/; max-age=31536000";
                localStorage.setItem('totem_lang', lang);
                const targetUrl = '<?= !empty($from) ? esc(base_url($from), 'js') : esc(base_url('menu'), 'js') ?>';
                if (window.totemNavigateTo) {
                    window.totemNavigateTo(targetUrl);
                } else {
                    window.location.href = targetUrl;
                }
            }

            window.setLanguage = setLanguage;

            const initLanguageInteractions = () => {
                const buttons = document.querySelectorAll('.pill-button--language');
                buttons.forEach(btn => {
                    const lang = btn.getAttribute('data-lang');
                    const instruction = document.querySelector(`.lang-instruction--${lang}`);
                    
                    if (instruction) {
                        const highlight = () => {
                            document.querySelectorAll('.language-instruction').forEach(el => {
                                el.classList.remove('is-highlighted');
                            });
                            instruction.classList.add('is-highlighted');
                        };
                        
                        const removeHighlight = () => {
                            instruction.classList.remove('is-highlighted');
                        };
                        
                        btn.addEventListener('mouseenter', highlight);
                        btn.addEventListener('mouseleave', removeHighlight);
                        btn.addEventListener('focus', highlight);
                        btn.addEventListener('blur', removeHighlight);
                        btn.addEventListener('touchstart', highlight);
                        btn.addEventListener('touchend', removeHighlight);
                    }
                });
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
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
