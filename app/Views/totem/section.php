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
                                <img data-school-person-modal-photo src="" alt="">
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
                                    <span class="school-course__tag"><?= esc($section['courseTag'] ?? lang('Section.course_tag')) ?></span>
                                    <h3 class="school-course__title"><?= esc($section['courseTitle'] ?? '') ?></h3>
                                    <p class="school-course__start"><?= esc($section['courseStart'] ?? '') ?></p>
                                    <p class="school-course__copy"><?= esc($section['courseCopy'] ?? '') ?></p>

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
                                                    src="<?= esc(base_url($section['courseQrImage'] ?? 'assets/img/school/teatroescuela-qr.png'), 'attr') ?>"
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
                if (typeof window.__totemSchoolPeopleModalCleanup === 'function') {
                    window.__totemSchoolPeopleModalCleanup();
                }

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

                const page = document.querySelector('.school-page');
                const modal = document.querySelector('[data-school-person-modal]');
                const modalPhoto = modal?.querySelector('[data-school-person-modal-photo]');
                const modalGroup = modal?.querySelector('[data-school-person-modal-group]');
                const modalName = modal?.querySelector('[data-school-person-modal-name]');
                const modalRole = modal?.querySelector('[data-school-person-modal-role]');
                const modalDescription = modal?.querySelector('[data-school-person-modal-description]');
                const closeTargets = modal ? modal.querySelectorAll('[data-school-person-modal-close]') : [];
                const triggers = document.querySelectorAll('[data-person-trigger]');

                if (!page || !modal || !modalPhoto || !modalGroup || !modalName || !modalRole || !modalDescription) {
                    return;
                }

                const previousBodyOverflow = document.body.style.overflow;
                let lastTrigger = null;
                const handleTriggerClick = (event) => {
                    openModal(event.currentTarget);
                };

                const openModal = (trigger) => {
                    lastTrigger = trigger;
                    modalPhoto.src = trigger.getAttribute('data-person-photo') || '';
                    modalPhoto.alt = trigger.getAttribute('data-person-alt') || '';
                    modalGroup.textContent = trigger.getAttribute('data-person-group') || '';
                    modalName.textContent = trigger.getAttribute('data-person-name') || '';
                    modalRole.textContent = trigger.getAttribute('data-person-role') || '';
                    modalDescription.textContent = trigger.getAttribute('data-person-description') || '';
                    modal.hidden = false;
                    modal.setAttribute('aria-hidden', 'false');
                    page.classList.add('school-page--modal-open');
                    document.body.style.overflow = 'hidden';

                    const firstFocus = modal.querySelector('.school-person-modal__close');
                    if (firstFocus) {
                        firstFocus.focus();
                    }
                };

                const closeModal = () => {
                    modal.hidden = true;
                    modal.setAttribute('aria-hidden', 'true');
                    page.classList.remove('school-page--modal-open');
                    document.body.style.overflow = previousBodyOverflow;

                    if (lastTrigger && typeof lastTrigger.focus === 'function') {
                        lastTrigger.focus();
                    }
                };

                const onKeyDown = (event) => {
                    if (!modal.hidden && event.key === 'Escape') {
                        event.preventDefault();
                        closeModal();
                    }
                };

                const onModalClick = (event) => {
                    if (event.target.closest('[data-school-person-modal-close]')) {
                        event.preventDefault();
                        closeModal();
                    }
                };

                triggers.forEach((trigger) => {
                    trigger.addEventListener('click', handleTriggerClick);
                });

                closeTargets.forEach((target) => {
                    target.addEventListener('click', (event) => {
                        event.preventDefault();
                        closeModal();
                    });
                });

                modal.addEventListener('click', onModalClick);
                document.addEventListener('keydown', onKeyDown);

                window.__totemSchoolPeopleModalCleanup = () => {
                    triggers.forEach((trigger) => {
                        trigger.removeEventListener('click', handleTriggerClick);
                    });
                    modal.removeEventListener('click', onModalClick);
                    document.removeEventListener('keydown', onKeyDown);
                    document.body.style.overflow = previousBodyOverflow;
                };
            });
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>
