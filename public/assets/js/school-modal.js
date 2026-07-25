(() => {
    const isMobile = window.matchMedia('(max-width: 560px) and (pointer: coarse)').matches;

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

    const closeHandlers = [];
    closeTargets.forEach((target) => {
        const handler = (event) => {
            event.preventDefault();
            closeModal();
        };
        target.addEventListener('click', handler);
        closeHandlers.push({ target, handler });
    });

    modal.addEventListener('click', onModalClick);
    document.addEventListener('keydown', onKeyDown);

    const cleanup = () => {
        triggers.forEach((trigger) => {
            trigger.removeEventListener('click', handleTriggerClick);
        });
        closeHandlers.forEach(({ target, handler }) => {
            target.removeEventListener('click', handler);
        });
        modal.removeEventListener('click', onModalClick);
        document.removeEventListener('keydown', onKeyDown);
        document.body.style.overflow = previousBodyOverflow;
    };

    if (Array.isArray(window.__totemCleanup)) {
        window.__totemCleanup.push(cleanup);
    }

    window.__totemSchoolPeopleModalCleanup = cleanup;
})();
