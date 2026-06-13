<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body billboard-detail">
            <section class="billboard-detail__intro" aria-label="<?= esc(lang('Billboard.editorial_intro_label'), 'attr') ?>">
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

            <section class="billboard-detail__media" aria-label="<?= esc(lang('Billboard.media_label'), 'attr') ?>">
                <figure class="billboard-detail__poster-frame" aria-label="<?= esc($detail['title'] ?? lang('Billboard.default_title'), 'attr') ?>">
                    <img
                        class="billboard-detail__poster"
                        src="<?= esc(base_url($detail['image'] ?? 'assets/img/menu/menu_programacion.webp'), 'attr') ?>"
                        alt="<?= esc($detail['title'] ?? lang('Billboard.default_title'), 'attr') ?>"
                    >
                </figure>

                <div class="billboard-detail__nav" aria-label="<?= esc(lang('Billboard.image_nav_label'), 'attr') ?>">
                    <button type="button" class="billboard-detail__nav-btn" aria-label="<?= esc(lang('Billboard.previous_image'), 'attr') ?>">
                        <img src="<?= esc(base_url('assets/img/ui/slider_left.webp'), 'attr') ?>" alt="" aria-hidden="true">
                    </button>
                    <button type="button" class="billboard-detail__nav-btn" aria-label="<?= esc(lang('Billboard.next_image'), 'attr') ?>">
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

                <aside class="billboard-detail__sidebar" aria-label="<?= esc(lang('Billboard.quick_sheet_label'), 'attr') ?>">
                    <div class="billboard-detail__schedule">
                        <span class="billboard-detail__date"><?= esc($detail['date'] ?? '') ?></span>
                        <span class="billboard-detail__time"><?= esc($detail['time'] ?? '') ?></span>
                    </div>

                    <span class="billboard-detail__rule" aria-hidden="true"></span>

                    <div class="billboard-detail__metric">
                        <img
                            class="billboard-detail__metric-icon"
                            src="<?= esc(base_url('assets/img/ui/icon_duration.webp'), 'attr') ?>"
                            alt=""
                            aria-hidden="true"
                        >
                        <div class="billboard-detail__metric-content">
                            <span class="billboard-detail__metric-label"><?= esc(lang('Billboard.duration_label')) ?></span>
                            <strong class="billboard-detail__metric-value"><?= esc($detail['duration'] ?? '') ?></strong>
                        </div>
                    </div>

                    <span class="billboard-detail__rule" aria-hidden="true"></span>

                    <div class="billboard-detail__metric">
                        <img
                            class="billboard-detail__metric-icon"
                            src="<?= esc(base_url('assets/img/ui/icon_ticket.webp'), 'attr') ?>"
                            alt=""
                            aria-hidden="true"
                        >
                        <div class="billboard-detail__metric-content">
                            <span class="billboard-detail__metric-label billboard-detail__metric-label--strong"><?= esc($detail['price'] ?? '') ?></span>
                            <strong class="billboard-detail__metric-value"><?= esc(lang('Billboard.price_note')) ?></strong>
                            <p class="billboard-detail__note"><?= esc(lang('Billboard.ticket_note')) ?></p>
                        </div>
                    </div>
                </aside>
            </section>

            <section class="billboard-detail__closing" aria-label="<?= esc(lang('Billboard.closing_label'), 'attr') ?>">
                <div class="billboard-detail__collage">
                    <img
                        class="billboard-detail__collage-image"
                        src="<?= esc(base_url($detail['closingImage'] ?? 'assets/img/splash/collage-inicio.webp'), 'attr') ?>"
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
                    <p class="billboard-detail__contact-copy"><?= esc($detail['closingNote'] ?? lang('Billboard.default_closing_note')) ?></p>
                </div>

            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Menu.programming'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
