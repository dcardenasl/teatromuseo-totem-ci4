<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-page language-layout">
    <section class="language-card language-card--bare">
        <h1 class="language-card__title language-card__title--spacious">Selecciona tu idioma</h1>

        <div class="language-grid language-grid--spacious" role="list" aria-label="Idiomas">
            <button class="pill-button pill-button--language" type="button" onclick="setLanguage('es')">
                Español
            </button>
            <button class="pill-button pill-button--language" type="button" onclick="setLanguage('en')">
                English
            </button>
            <button class="pill-button pill-button--language" type="button" onclick="setLanguage('fr')">
                Français
            </button>
            <button class="pill-button pill-button--language" type="button" onclick="setLanguage('pt')">
                Português
            </button>
        </div>
    </section>
</div>

<script>
function setLanguage(lang) {
    localStorage.setItem('totem_lang', lang);
    window.location.href = '<?= !empty($from) ? esc(base_url($from), 'js') : esc(base_url('menu'), 'js') ?>';
}
</script>
<?= $this->endSection() ?>
