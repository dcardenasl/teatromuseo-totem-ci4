<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <?php if (isset($courses)): ?>
                <section class="school-page" aria-label="<?= esc(lang('Section.school_aria_label'), 'attr') ?>">
                    <div class="school-page__hero">
                        <figure class="school-video" aria-label="<?= esc($section['heroAlt'] ?? lang('Section.school_aria_label'), 'attr') ?>">
                            <img
                                class="school-video__image"
                                src="<?= esc(base_url($section['heroImage'] ?? 'assets/img/menu/menu_escuela.webp'), 'attr') ?>"
                                alt="<?= esc($section['heroAlt'] ?? lang('Section.school_aria_label'), 'attr') ?>"
                            >
                            <span class="school-video__play" aria-hidden="true">
                                <span class="school-video__play-triangle"></span>
                            </span>
                        </figure>

                        <p class="school-page__intro"><?= esc($section['introCopy'] ?? '') ?></p>

                        <div class="school-stats" aria-label="<?= esc(lang('Section.key_stats_label'), 'attr') ?>">
                            <?php foreach (($section['stats'] ?? []) as $stat): ?>
                                <div class="school-stat">
                                    <span class="school-stat__value"><?= esc($stat['value'] ?? '') ?></span>
                                    <span class="school-stat__divider" aria-hidden="true"></span>
                                    <span class="school-stat__label"><?= esc($stat['label'] ?? '') ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <section class="school-people-section" aria-label="<?= esc(lang('Section.teachers_label'), 'attr') ?>">
                        <h2 class="school-section-title"><?= esc($section['teachersTitle'] ?? lang('Section.teachers_label')) ?></h2>

                        <div class="school-people-rail" role="list" aria-label="<?= esc(lang('Section.teachers_label'), 'attr') ?>">
                            <?php foreach ($teachers as $teacher): ?>
                                <button
                                    type="button"
                                    class="teacher-card teacher-card--interactive <?= esc($teacher['tone'] ?? '') ?>"
                                    role="listitem"
                                    aria-haspopup="dialog"
                                    aria-controls="school-person-modal"
                                    data-person-trigger
                                    data-person-group="<?= esc($section['teachersTitle'] ?? lang('Section.teachers_label'), 'attr') ?>"
                                    data-person-name="<?= esc($teacher['name'], 'attr') ?>"
                                    data-person-role="<?= esc($teacher['role'], 'attr') ?>"
                                    data-person-description="<?= esc($teacher['description'], 'attr') ?>"
                                    data-person-photo="<?= esc(base_url($personPhoto), 'attr') ?>"
                                    data-person-alt="<?= esc($teacher['name'], 'attr') ?>"
                                >
                                    <span class="teacher-card__media" aria-hidden="true">
                                        <img
                                            src="<?= esc(base_url($personPhoto), 'attr') ?>"
                                            alt=""
                                        >
                                    </span>
                                    <span class="teacher-card__body">
                                        <span class="teacher-card__name"><?= esc($teacher['name']) ?></span>
                                        <span class="teacher-card__role"><?= esc($teacher['role']) ?></span>
                                        <span class="teacher-card__country"><?= esc(lang('Section.school_person_card_cta')) ?></span>
                                    </span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="school-people-section" aria-label="<?= esc(lang('Section.school_students_title'), 'attr') ?>">
                        <h2 class="school-section-title"><?= esc($section['studentsTitle'] ?? lang('Section.school_students_title')) ?></h2>

                        <div class="school-people-rail" role="list" aria-label="<?= esc(lang('Section.school_students_title'), 'attr') ?>">
                            <?php foreach ($students as $student): ?>
                                <button
                                    type="button"
                                    class="teacher-card teacher-card--interactive <?= esc($student['tone'] ?? '') ?>"
                                    role="listitem"
                                    aria-haspopup="dialog"
                                    aria-controls="school-person-modal"
                                    data-person-trigger
                                    data-person-group="<?= esc($section['studentsTitle'] ?? lang('Section.school_students_title'), 'attr') ?>"
                                    data-person-name="<?= esc($student['name'], 'attr') ?>"
                                    data-person-role="<?= esc($student['role'], 'attr') ?>"
                                    data-person-description="<?= esc($student['description'], 'attr') ?>"
                                    data-person-photo="<?= esc(base_url($personPhoto), 'attr') ?>"
                                    data-person-alt="<?= esc($student['name'], 'attr') ?>"
                                >
                                    <span class="teacher-card__media" aria-hidden="true">
                                        <img
                                            src="<?= esc(base_url($personPhoto), 'attr') ?>"
                                            alt=""
                                        >
                                    </span>
                                    <span class="teacher-card__body">
                                        <span class="teacher-card__name"><?= esc($student['name']) ?></span>
                                        <span class="teacher-card__role"><?= esc($student['role']) ?></span>
                                        <span class="teacher-card__country"><?= esc(lang('Section.school_person_card_cta')) ?></span>
                                    </span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <div class="school-person-modal" id="school-person-modal" data-school-person-modal hidden aria-hidden="true">
                        <div class="school-person-modal__backdrop" data-school-person-modal-close></div>
                        <div class="school-person-modal__panel" role="dialog" aria-modal="true" aria-labelledby="school-person-modal-title" aria-describedby="school-person-modal-description">
                            <button
                                type="button"
                                class="school-person-modal__close"
                                data-school-person-modal-close
                                aria-label="<?= esc(lang('Section.school_person_modal_close'), 'attr') ?>"
                            >
                                ×
                            </button>

                            <div class="school-person-modal__media">
                                <img data-school-person-modal-photo alt="">
                            </div>

                            <div class="school-person-modal__content">
                                <p class="school-person-modal__group" data-school-person-modal-group></p>
                                <h3 id="school-person-modal-title" class="school-person-modal__name" data-school-person-modal-name></h3>
                                <p class="school-person-modal__role" data-school-person-modal-role></p>
                                <p id="school-person-modal-description" class="school-person-modal__description" data-school-person-modal-description></p>
                            </div>
                        </div>
                    </div>

                    <section class="school-courses" aria-label="<?= esc(lang('Section.courses_label'), 'attr') ?>">
                        <h2 class="school-section-title school-section-title--course"><?= esc($section['coursesTitle'] ?? lang('Section.courses_label')) ?></h2>

                        <?php foreach ($courses as $course): ?>
                            <article class="school-course">
                                <div class="school-course__poster">
                                    <img
                                        src="<?= esc(base_url($section['courseImage'] ?? 'assets/img/menu/menu_programacion.webp'), 'attr') ?>"
                                        alt="<?= esc($section['courseTitle'] ?? lang('Section.course_title_placeholder'), 'attr') ?>"
                                    >
                                </div>

                                <div class="school-course__body">
                                    <span class="school-course__tag"><?= esc($course['tag'] ?? $section['courseTag'] ?? lang('Section.course_tag')) ?></span>
                                    <h3 class="school-course__title"><?= esc($course['title'] ?? $section['courseTitle'] ?? '') ?></h3>
                                    <p class="school-course__start"><?= esc($course['start'] ?? $section['courseStart'] ?? '') ?></p>
                                    <p class="school-course__copy"><?= esc($course['copy'] ?? $section['courseCopy'] ?? '') ?></p>

                                    <div class="school-course__contact">
                                        <div class="school-course__contact-copy">
                                            <span class="school-course__contact-label"><?= esc($section['courseContactLabel'] ?? lang('Section.course_contact_label')) ?></span>
                                            <span class="school-course__contact-value"><?= esc($section['courseContact'] ?? '') ?></span>
                                        </div>

                                        <div class="school-course__qr">
                                            <a
                                                class="school-course__qr-link"
                                                data-qr-url="<?= esc($section['courseQrUrl'] ?? '#', 'attr') ?>"
                                                aria-label="<?= esc(lang('Section.course_qr_action_label'), 'attr') ?>"
                                            >
                                                <img
                                                    class="school-course__qr-box"
                                                    src="<?= esc(base_url($section['courseQrImage'] ?? 'assets/img/school/teatroescuela-qr.webp'), 'attr') ?>"
                                                    alt="<?= esc(lang('Section.course_qr_alt'), 'attr') ?>"
                                                >
                                            </a>
                                            <span class="school-course__qr-label"><?= esc($section['courseQrLabel'] ?? lang('Section.course_qr_label')) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </section>

                </section>
        </div>

    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => esc($section['title']),
        'content' => $content,
        'nav' => $nav ?? [],
        'footerVariant' => 'school',
    ]) ?>

    <script src="<?= base_url('assets/js/school-modal.js') ?>"></script>
<?php endif; ?>
<?= $this->endSection() ?>
