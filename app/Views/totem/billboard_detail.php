<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body billboard-detail">
            <section class="billboard-detail__intro" aria-label="Cabecera editorial de cartelera">
                <div class="billboard-detail__tags">
                    <?php foreach (($detail['tags'] ?? []) as $tag): ?>
                        <span class="billboard-detail__tag chip"><?= esc($tag) ?></span>
                    <?php endforeach; ?>
                </div>

                <h2 class="billboard-detail__show-title"><?= esc($detail['title'] ?? '') ?></h2>

                <p class="billboard-detail__meta">
                    <strong><?= esc($detail['company'] ?? '') ?></strong>
                    <span><?= esc($detail['direction'] ?? '') ?></span>
                </p>
            </section>

            <section class="billboard-detail__media" aria-label="Imagen principal de cartelera">
                <figure class="billboard-detail__poster-frame" aria-label="<?= esc($detail['title'] ?? 'Obra', 'attr') ?>">
                    <img
                        class="billboard-detail__poster"
                        src="<?= esc(base_url($detail['image'] ?? 'assets/img/menu/menu_programacion.webp'), 'attr') ?>"
                        alt="<?= esc($detail['title'] ?? 'Obra', 'attr') ?>"
                    >
                </figure>

                <div class="billboard-detail__nav" aria-label="Navegación de imágenes">
                    <button type="button" class="billboard-detail__nav-btn" aria-label="Imagen anterior">
                        <img src="<?= esc(base_url('assets/img/ui/slider_left.webp'), 'attr') ?>" alt="" aria-hidden="true">
                    </button>
                    <button type="button" class="billboard-detail__nav-btn" aria-label="Imagen siguiente">
                        <img src="<?= esc(base_url('assets/img/ui/slider_right.webp'), 'attr') ?>" alt="" aria-hidden="true">
                    </button>
                </div>
            </section>

            <section class="billboard-detail__body">
                <div class="billboard-detail__copy">
                    <p class="billboard-detail__lead">
                        <?= esc($detail['copy'] ?? '') ?>
                    </p>
                </div>

                <aside class="billboard-detail__sidebar" aria-label="Ficha rápida">
                    <div class="billboard-detail__schedule">
                        <span class="billboard-detail__date"><?= esc($detail['date'] ?? '') ?></span>
                        <span class="billboard-detail__time"><?= esc($detail['time'] ?? '') ?></span>
                    </div>

                    <span class="billboard-detail__rule" aria-hidden="true"></span>

                    <div class="billboard-detail__metric">
                        <span class="billboard-detail__metric-head">
                            <img src="<?= esc(base_url('assets/img/ui/icon_duration.webp'), 'attr') ?>" alt="" aria-hidden="true">
                            <span>Duración aproximada:</span>
                        </span>
                        <strong class="billboard-detail__metric-value"><?= esc($detail['duration'] ?? '') ?></strong>
                    </div>

                    <span class="billboard-detail__rule" aria-hidden="true"></span>

                    <div class="billboard-detail__metric">
                        <span class="billboard-detail__metric-head billboard-detail__metric-head--strong">
                            <img src="<?= esc(base_url('assets/img/ui/icon_ticket.webp'), 'attr') ?>" alt="" aria-hidden="true">
                            <span><?= esc($detail['price'] ?? '') ?></span>
                        </span>
                        <strong class="billboard-detail__metric-value">Niños, estudiantes y 3 edad: $3.500</strong>
                    </div>

                    <p class="billboard-detail__note">*Las entradas se adquieren 20 minutos antes de la función en la boletería de Teatromuseo.</p>
                </aside>
            </section>

            <section class="billboard-detail__closing" aria-label="Cierre editorial">
                <div class="billboard-detail__collage">
                    <img
                        class="billboard-detail__collage-image"
                        src="<?= esc(base_url($detail['closingImage'] ?? 'assets/img/menu/collage_referencia.webp'), 'attr') ?>"
                        alt=""
                        aria-hidden="true"
                    >
                </div>

                <div class="billboard-detail__contact">
                    <img
                        class="billboard-detail__qr-image"
                        src="<?= esc(base_url($detail['qrImage'] ?? 'assets/img/school/teatroescuela-qr.png'), 'attr') ?>"
                        alt=""
                        aria-hidden="true"
                    >
                    <p class="billboard-detail__contact-copy"><?= esc($detail['closingNote'] ?? 'Síguenos en Instagram y entérate de más detalles') ?></p>
                </div>

            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => 'CARTELERA',
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
