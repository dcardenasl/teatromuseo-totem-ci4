<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php
    $today = $today ?? [];
    $stats = $today['stats'] ?? [];
    $actions = $today['actions'] ?? [];
    $blocks = $today['blocks'] ?? [];
    $primary = $today['primary'] ?? [];
    ?>

    <?php ob_start(); ?>
        <div class="screen-page__body museum-today">
            <section class="museum-today__hero screen__panel" aria-labelledby="museum-today-title">
                <div class="museum-today__visual" aria-hidden="true">
                    <img src="<?= base_url($today['image'] ?? 'assets/img/museum/cat_el_museo.webp') ?>" alt="">
                    <span class="museum-today__badge"><?= esc(lang('MuseumInfo.today_feature_badge')) ?></span>
                </div>

                <div class="museum-today__copy">
                    <p class="museum-today__eyebrow"><?= esc($today['eyebrow'] ?? lang('MuseumInfo.today_eyebrow')) ?></p>
                    <h2 class="museum-today__title" id="museum-today-title"><?= esc($today['headline'] ?? lang('MuseumInfo.today_title')) ?></h2>
                    <p class="museum-today__intro"><?= esc($today['intro'] ?? lang('MuseumInfo.today_intro')) ?></p>

                    <?php if (!empty($stats)): ?>
                        <div class="stat-grid museum-today__stats">
                            <?php foreach ($stats as $stat): ?>
                                <div class="stat-card museum-today__stat">
                                    <span class="stat-card__label"><?= esc($stat['label'] ?? '') ?></span>
                                    <span class="stat-card__value"><?= esc($stat['value'] ?? '') ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($actions)): ?>
                        <div class="museum-today__actions" aria-label="<?= esc(lang('MuseumInfo.today_actions_label')) ?>">
                            <?php foreach ($actions as $action): ?>
                                <a class="pill-button" href="<?= esc($action['href'] ?? '#') ?>">
                                    <?= esc($action['label'] ?? '') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="museum-today__content" aria-label="<?= esc(lang('MuseumInfo.today_blocks_heading')) ?>">
                <article class="museum-today__feature content-panel content-panel--soft">
                    <p class="museum-today__kicker"><?= esc(lang('MuseumInfo.today_feature_label')) ?></p>
                    <h3 class="museum-today__feature-title"><?= esc($primary['title'] ?? lang('MuseumInfo.today_empty_title')) ?></h3>
                    <p class="museum-today__feature-copy">
                        <?= esc($primary['copy'] ?? lang('MuseumInfo.today_empty_copy')) ?>
                    </p>
                    <p class="museum-today__feature-note">
                        <?= esc(lang('MuseumInfo.today_feature_note')) ?>
                    </p>
                </article>

                <div class="museum-today__stack">
                    <div class="museum-today__stack-head">
                        <p class="museum-today__kicker"><?= esc(lang('MuseumInfo.today_blocks_heading')) ?></p>
                        <p class="museum-today__stack-copy"><?= esc(lang('MuseumInfo.today_blocks_copy')) ?></p>
                    </div>

                    <?php if (!empty($blocks)): ?>
                        <?php foreach ($blocks as $block): ?>
                            <article class="museum-today__block <?= !empty($block['fallback']) ? 'museum-today__block--fallback' : '' ?>">
                                <span class="museum-today__block-index"><?= esc($block['index'] ?? '00') ?></span>
                                <h4 class="museum-today__block-title"><?= esc($block['title'] ?? '') ?></h4>
                                <p class="museum-today__block-copy"><?= esc($block['copy'] ?? '') ?></p>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('MuseumInfo.main_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
