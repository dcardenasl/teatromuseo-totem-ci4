<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <section class="billboard-months" aria-label="<?= esc(lang('Billboard.available_dates_label'), 'attr') ?>">
                <?php foreach ($months as $month): ?>
                    <div class="month-group">
                        <span class="month-group__title"><?= esc($month['title']) ?></span>
                        <div class="month-group__chips">
                            <?php foreach ($month['days'] as $day): ?>
                                <span class="date-chip"><?= esc($day) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="event-list" aria-label="<?= esc(lang('Billboard.events_label'), 'attr') ?>">
                <?php foreach ($events as $event): ?>
                    <a class="event-card <?= esc($event['class']) ?>" href="<?= base_url('cartelera/detalle/' . esc($event['slug'] ?? '1')) ?>">
                        <div class="event-card__media" aria-hidden="true"></div>
                        <div class="event-card__body">
                            <div class="event-card__meta">
                                <span class="chip"><?= esc($event['tag']) ?></span>
                                <span class="chip"><?= esc($event['type']) ?></span>
                            </div>
                            <h2 class="event-card__title"><?= esc($event['title']) ?></h2>
                            <p class="event-card__copy"><?= esc($event['copy']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Menu.programming'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
