<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <?php if (isset($courses)): ?>
                <section class="school-layout" aria-label="Próximos cursos">
                    <div class="section-hero__visual section-hero__visual--school section-hero__visual--school-compact" aria-hidden="true"></div>

                    <div class="course-list" role="list">
                        <?php foreach ($courses as $course): ?>
                            <article class="course-card" role="listitem">
                                <div class="course-card__thumb" aria-hidden="true"></div>
                                <div class="course-card__body">
                                    <span class="chip"><?= esc($course['tag']) ?></span>
                                    <h2 class="course-card__title"><?= esc($course['title']) ?></h2>
                                    <p class="course-card__date"><?= esc($course['start']) ?></p>
                                    <p class="course-card__copy"><?= esc($course['copy']) ?></p>
                                </div>
                                <div class="course-card__qr" aria-hidden="true">
                                    <span class="course-card__qr-box"></span>
                                    <span class="course-card__qr-label">Más info</span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php else: ?>
                <section class="content-grid content-grid--section">
                    <div class="<?= esc($section['visualClass']) ?>" aria-hidden="true"></div>

                    <article class="content-panel content-panel--soft">
                        <h2 class="content-panel__title"><?= esc($section['detailsTitle']) ?></h2>
                        <p class="content-panel__text"><?= esc($section['detailsCopy']) ?></p>

                        <?php if (!empty($section['stats'])): ?>
                            <div class="stat-grid stat-grid--compact">
                                <?php foreach ($section['stats'] as $stat): ?>
                                    <div class="stat-card">
                                        <span class="stat-card__label"><?= esc($stat['label']) ?></span>
                                        <span class="stat-card__value"><?= esc($stat['value']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                </section>
            <?php endif; ?>
        </div>

        <?= $this->include('totem/partials/page_footer', ['variant' => isset($courses) ? 'school' : 'section']) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => esc($section['title']),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
