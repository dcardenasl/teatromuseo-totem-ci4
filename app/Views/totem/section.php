<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <?php if (isset($courses)): ?>
                <section class="school-page" aria-label="Teatro escuela">
                    <div class="school-page__hero">
                        <figure class="school-video" aria-label="<?= esc($section['heroAlt'] ?? 'Teatro escuela', 'attr') ?>">
                            <img
                                class="school-video__image"
                                src="<?= esc(base_url($section['heroImage'] ?? 'assets/img/menu/menu_escuela.webp'), 'attr') ?>"
                                alt="<?= esc($section['heroAlt'] ?? 'Teatro escuela', 'attr') ?>"
                            >
                            <span class="school-video__play" aria-hidden="true">
                                <span class="school-video__play-triangle"></span>
                            </span>
                        </figure>

                        <p class="school-page__intro"><?= esc($section['introCopy'] ?? '') ?></p>

                        <div class="school-stats" aria-label="Cifras clave">
                            <?php foreach (($section['stats'] ?? []) as $stat): ?>
                                <div class="school-stat">
                                    <span class="school-stat__value"><?= esc($stat['value'] ?? '') ?></span>
                                    <span class="school-stat__divider" aria-hidden="true"></span>
                                    <span class="school-stat__label"><?= esc($stat['label'] ?? '') ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <section class="school-teachers" aria-label="Maestros">
                        <h2 class="school-section-title"><?= esc($section['teachersTitle'] ?? 'Maestros') ?></h2>

                        <div class="school-teacher-grid" role="list">
                            <?php foreach ($teachers as $index => $teacher): ?>
                                <article class="teacher-card <?= esc($teacher['tone'] ?? '') ?>" role="listitem">
                                    <div class="teacher-card__media" aria-hidden="true">
                                        <img
                                            src="<?= esc(base_url($section['heroImage'] ?? 'assets/img/menu/menu_escuela.webp'), 'attr') ?>"
                                            alt=""
                                        >
                                    </div>
                                    <div class="teacher-card__body">
                                        <h3 class="teacher-card__name">NOMBRE APELLIDO</h3>
                                        <p class="teacher-card__role">Especialidad</p>
                                        <p class="teacher-card__country">País</p>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <div class="school-dots" aria-hidden="true">
                            <span class="school-dots__dot is-active"></span>
                            <span class="school-dots__dot"></span>
                            <span class="school-dots__dot"></span>
                            <span class="school-dots__dot"></span>
                            <span class="school-dots__dot"></span>
                        </div>
                    </section>

                    <section class="school-courses" aria-label="Cursos">
                        <h2 class="school-section-title school-section-title--course"><?= esc($section['coursesTitle'] ?? 'Cursos') ?></h2>

                        <?php foreach ($courses as $course): ?>
                            <article class="school-course">
                                <div class="school-course__poster">
                                    <img
                                        src="<?= esc(base_url($section['courseImage'] ?? 'assets/img/menu/menu_programacion.webp'), 'attr') ?>"
                                        alt="<?= esc($section['courseTitle'] ?? 'Curso', 'attr') ?>"
                                    >
                                </div>

                                <div class="school-course__body">
                                    <span class="school-course__tag"><?= esc($section['courseTag'] ?? 'Nacional') ?></span>
                                    <h3 class="school-course__title"><?= esc($section['courseTitle'] ?? '') ?></h3>
                                    <p class="school-course__start"><?= esc($section['courseStart'] ?? '') ?></p>
                                    <p class="school-course__copy"><?= esc($section['courseCopy'] ?? '') ?></p>

                                    <div class="school-course__contact">
                                        <div class="school-course__contact-copy">
                                            <span class="school-course__contact-label"><?= esc($section['courseContactLabel'] ?? 'Correo de contacto:') ?></span>
                                            <span class="school-course__contact-value"><?= esc($section['courseContact'] ?? '') ?></span>
                                        </div>

                                        <div class="school-course__qr">
                                            <a
                                                class="school-course__qr-link"
                                                data-qr-url="<?= esc($section['courseQrUrl'] ?? '#', 'attr') ?>"
                                                aria-label="Abrir información de Teatro Escuela"
                                            >
                                                <img
                                                    class="school-course__qr-box"
                                                    src="<?= esc(base_url($section['courseQrImage'] ?? 'assets/img/school/teatroescuela-qr.png'), 'attr') ?>"
                                                    alt="QR a Teatro Escuela"
                                                >
                                            </a>
                                            <span class="school-course__qr-label"><?= esc($section['courseQrLabel'] ?? 'Más información') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </section>

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

    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => esc($section['title']),
        'content' => $content,
        'nav' => $nav ?? [],
        'footerVariant' => isset($courses) ? 'school' : 'section',
    ]) ?>

    <?php if (isset($courses)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const isMobile = window.matchMedia('(max-width: 820px) and (pointer: coarse)').matches;
                document.querySelectorAll('.school-course__qr-link[data-qr-url]').forEach((link) => {
                    const url = link.getAttribute('data-qr-url');

                    if (isMobile && url) {
                        link.href = url;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                        link.setAttribute('aria-disabled', 'false');
                        link.style.pointerEvents = 'auto';
                    } else {
                        link.removeAttribute('href');
                        link.removeAttribute('target');
                        link.removeAttribute('rel');
                        link.setAttribute('aria-disabled', 'true');
                        link.style.pointerEvents = 'none';
                        link.style.cursor = 'default';
                    }
                });
            });
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>
