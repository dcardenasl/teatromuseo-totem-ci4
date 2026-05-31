<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <section class="detail-layout">
                <article class="detail-main">
                    <div class="detail-meta">
                        <?php foreach ($detail['tags'] as $tag): ?>
                            <span class="chip"><?= esc($tag) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <p class="detail-main__lead">
                        <strong><?= esc($detail['company']) ?></strong><br>
                        <?= esc($detail['direction']) ?>
                    </p>

                    <div class="detail-slider">
                        <button type="button" class="slider-btn slider-btn--left" aria-label="Anterior">
                            <img src="<?= base_url('assets/img/ui/slider_left.webp') ?>" alt="←">
                        </button>
                        <div class="detail-hero__image">
                            <img src="<?= base_url('assets/img/menu/menu_programacion.webp') ?>" alt="Obra" class="slider-img">
                        </div>
                        <button type="button" class="slider-btn slider-btn--right" aria-label="Siguiente">
                            <img src="<?= base_url('assets/img/ui/slider_right.webp') ?>" alt="→">
                        </button>
                    </div>

                    <p class="content-panel__text"><?= esc($detail['copy']) ?></p>
                </article>

                <aside class="detail-sidebar">
                    <div class="detail-stat">
                        <span class="detail-stat__label"><?= esc($detail['date']) ?></span>
                        <span class="detail-stat__value"><?= esc($detail['time']) ?></span>
                    </div>
                    <div class="detail-stat">
                        <span class="detail-stat__label-container">
                            <img src="<?= base_url('assets/img/ui/icon_duration.webp') ?>" alt="Duración" class="detail-stat__icon">
                            <span class="detail-stat__label">Duración aproximada</span>
                        </span>
                        <span class="detail-stat__value"><?= esc($detail['duration']) ?></span>
                    </div>
                    <div class="detail-stat">
                        <span class="detail-stat__label-container">
                            <img src="<?= base_url('assets/img/ui/icon_ticket.webp') ?>" alt="Entrada" class="detail-stat__icon">
                            <span class="detail-stat__label"><?= esc($detail['price']) ?></span>
                        </span>
                        <span class="detail-stat__value">Niños, estudiantes y 3 edad: $3.500</span>
                    </div>

                    <p class="detail-sidebar__note">*Las entradas se adquieren 20 minutos antes de la función en la boletería de Teatromuseo.</p>
                </aside>
            </section>
        </div>

        <?= $this->include('totem/partials/page_footer', ['variant' => 'detail']) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => esc($detail['title']),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
