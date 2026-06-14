<?php
/**
 * @var array<int, array{title:string, days:array<int, string>}> $months
 * @var array<int, array{class?:string, tone?:string, slug:string, image?:string, title:string, tag:string, type:string, dateLabel?:string, timeLabel?:string, copy:string}> $events
 * @var array $nav
 * @var string $titleClass
 * @var string $titleWidth
 * @var string $footerVariant
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body billboard-page">
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
                    <a class="event-card <?= esc($event['class'] ?? '') ?> <?= esc($event['tone'] ?? '') ?>" href="<?= base_url('cartelera/detalle/' . esc($event['slug'] ?? '1')) ?>">
                        <div class="event-card__media">
                            <?php if (!empty($event['image'])): ?>
                                <img
                                    class="event-card__image"
                                    src="<?= esc(base_url($event['image']), 'attr') ?>"
                                    alt="<?= esc($event['title'] ?? lang('Billboard.default_title'), 'attr') ?>"
                                >
                            <?php endif; ?>
                        </div>
                        <div class="event-card__body">
                            <div class="event-card__meta">
                                <span class="chip"><?= esc($event['tag']) ?></span>
                                <span class="chip"><?= esc($event['type']) ?></span>
                            </div>
                            <h2 class="event-card__title"><?= esc($event['title']) ?></h2>
                            <p class="event-card__schedule">
                                <span class="event-card__schedule-date"><?= esc($event['dateLabel'] ?? '') ?></span>
                                <span class="event-card__schedule-time"><?= esc($event['timeLabel'] ?? '') ?></span>
                            </p>
                            <p class="event-card__copy"><?= esc($event['copy']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>

            <section class="billboard-closing" aria-label="<?= esc(lang('Billboard.closing_label'), 'attr') ?>">
                <img
                    class="billboard-closing__image"
                    src="<?= esc(base_url('assets/img/billboard/cartelera-closing.webp'), 'attr') ?>"
                    alt=""
                    aria-hidden="true"
                >
            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Menu.programming'),
        'content' => $content,
        'nav' => $nav ?? [],
        'titleClass' => $titleClass ?? '',
        'titleWidth' => $titleWidth ?? '',
        'footerVariant' => $footerVariant ?? 'section',
    ]) ?>
<?= $this->endSection() ?>
