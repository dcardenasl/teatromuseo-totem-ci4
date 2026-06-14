<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <!-- Language selector modal.
             - data-on-select: URL to navigate to after selecting a language (always /menu)
             - data-on-cancel: URL to navigate to when closing with X button (back to origin)
        -->
        <div class="language-layout" data-on-select="<?= esc(base_url('menu'), 'attr') ?>" data-on-cancel="<?= esc(!empty($from) ? base_url($from) : base_url('menu'), 'attr') ?>">
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


    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => '',
        'content' => $content,
        'nav' => $nav ?? [],
        'chromeHidden' => true
    ]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/language-selector.js') ?>"></script>
<?= $this->endSection() ?>
