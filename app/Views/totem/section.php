<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-page">
    <div class="screen-page screen-page--section">
        <?= $this->include('totem/partials/topbar') ?>

        <header class="menu-title menu-title--compact">
            <span class="menu-title__eyebrow"><?= esc($section['eyebrow'] ?? 'Sección') ?></span>
            <h1 class="menu-title__heading menu-title__heading--compact"><?= esc($section['title']) ?></h1>
            <?php if (!empty($section['copy'])): ?>
                <p class="menu-title__copy"><?= esc($section['copy']) ?></p>
            <?php endif; ?>
        </header>

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
    </div>
</div>
<?= $this->endSection() ?>
