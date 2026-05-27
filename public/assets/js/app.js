let idleTime = 0;
const IDLE_LIMIT = 120; // 2 minutes in seconds

function resetTimer() {
    idleTime = 0;
}

// Reset timer on any interaction
['mousedown', 'touchstart', 'keypress', 'pointerdown', 'mousemove', 'scroll', 'focusin'].forEach((eventName) => {
    document.addEventListener(eventName, resetTimer, { passive: true });
});

setInterval(function() {
    idleTime++;
    if (idleTime >= IDLE_LIMIT) {
        // Redirect to splash screen only if not already there
        if (window.location.pathname !== '/' && window.location.pathname !== '/index.php/') {
            window.location.href = '/';
        }
    }
}, 1000);

document.addEventListener("DOMContentLoaded", () => {
    const lang = localStorage.getItem('totem_lang') || 'es';
    
    // 1. Translate Topbar
    const backBtn = document.querySelector('.pill-button--back span:not(.pill-button__icon)');
    const langBtn = document.querySelector('.pill-button--lang span:not(.pill-button__icon)');
    const homeBtn = document.querySelector('.pill-button--home span:not(.pill-button__icon)');

    const topbarTranslations = {
        es: { back: 'VOLVER', home: 'INICIO', lang: 'ESP' },
        en: { back: 'BACK', home: 'HOME', lang: 'ENG' },
        fr: { back: 'RETOUR', home: 'ACCUEIL', lang: 'FRA' },
        pt: { back: 'VOLTAR', home: 'INÍCIO', lang: 'POR' }
    };

    const trans = topbarTranslations[lang] || topbarTranslations.es;
    if (backBtn) backBtn.textContent = trans.back;
    if (langBtn) langBtn.textContent = trans.lang;
    if (homeBtn) homeBtn.textContent = trans.home;

    if (lang === 'es') return; // Default page text is Spanish, skip dictionary mapping

    // 2. Translate Page Content (Dictionary based on Spanish text)
    const dictionary = {
        en: {
            'Visitas guiadas': 'Guided Visits',
            'Recorridos y mediación': 'Tours and Mediation',
            'Una variante más breve del módulo de museo, útil para grupos y reservas. Sirve para recuperar el lenguaje ornamental de la propuesta con una acción clara.': 'A shorter version of the museum module, useful for groups and reservations. It serves to recover the ornamental style of the design with a clear call to action.',
            'Cómo funciona': 'How it works',
            'Bloques cortos, tarjetas por tipo de recorrido y una llamada a reserva para que la navegación sea inmediata desde pantalla táctil.': 'Short blocks, cards by tour type, and a booking link for immediate touch screen navigation.',
            'Reservas': 'Reservations',
            'Con anticipación': 'In advance',
            'Público': 'Audience',
            'Escolar y general': 'School & general',
            'Modalidad': 'Format',
            'Presencial': 'In person',
            'Explora el museo': 'Explore the museum',
            'Colección': 'Collection',
            'Recorre la colección viva': 'Tour the living collection',
            'Historia Cómica': 'Comic History',
            'Memoria del Circo y Clown': 'Circus and Clown Memory',
            'El Museo': 'The Museum',
            'Sobre el Espacio': 'About the Space',
            'Historia': 'History',
            'Memoria y origen': 'Memory and Origin',
            'Teatro escuela': 'Theater School',
            'Formación y mediación': 'Training and Mediation',
            'Amigos de Teatromuseo': 'Friends of Teatromuseo',
            'Comunidad y apoyo': 'Community and Support',
            'Cartelera': 'Billboard',
            'Programación': 'Programming',
            'Detalle de cartelera': 'Billboard Detail'
        },
        fr: {
            'Visitas guiadas': 'Visites Guidées',
            'Recorridos y mediación': 'Visites et Médiation',
            'Una variante más breve del módulo de museo, útil para grupos y reservas. Sirve para recuperar el lenguaje ornamental de la propuesta con una acción clara.': 'Une version plus courte du module musée, utile pour les groupes et les réservations. Elle sert à retrouver le style ornemental du projet avec une action claire.',
            'Cómo funciona': 'Comment ça fonctionne',
            'Bloques cortos, tarjetas por tipo de recorrido y una llamada a reserva para que la navegación sea inmediata desde pantalla táctil.': 'Blocs courts, fiches par type de visite et appel de réservation pour une navigation tactile immédiate.',
            'Reservas': 'Réservations',
            'Con anticipación': 'À l\'avance',
            'Público': 'Public',
            'Escolar y general': 'Scolaire & général',
            'Modalidad': 'Format',
            'Presencial': 'Présentiel',
            'Explora el museo': 'Explorer le musée',
            'Colección': 'Collection',
            'Recorre la colección viva': 'Parcourir la collection vivante',
            'Historia Cómica': 'Histoire Comique',
            'Memoria del Circo y Clown': 'Mémoire du Cirque et du Clown',
            'El Museo': 'Le Musée',
            'Sobre el Espacio': 'À propos de l\'espace',
            'Historia': 'Histoire',
            'Memoria y origen': 'Mémoire et Origine',
            'Teatro escuela': 'École de Théâtre',
            'Formación y mediación': 'Formation et Médiation',
            'Amigos de Teatromuseo': 'Amis du Théâtre-Musée',
            'Comunidad y apoyo': 'Communauté et Soutien',
            'Cartelera': 'Affiche',
            'Programación': 'Programmation',
            'Detalle de cartelera': 'Détail de l\'affiche'
        },
        pt: {
            'Visitas guiadas': 'Visitas Guiadas',
            'Recorridos y mediación': 'Roteiros e Mediação',
            'Una variante más breve del módulo de museo, útil para grupos y reservas. Sirve para recuperar el lenguaje ornamental de la propuesta con una acción clara.': 'Uma versão mais curta do módulo do museu, útil para grupos e reservas. Serve para recuperar o estilo ornamental do projeto com uma ação clara.',
            'Cómo funciona': 'Como funciona',
            'Bloques cortos, tarjetas por tipo de recorrido y una llamada a reserva para que la navegación sea inmediata desde pantalla táctil.': 'Blocos curtos, cartões por tipo de roteiro e chamada para reserva para navegação imediata na tela sensível ao toque.',
            'Reservas': 'Reservas',
            'Con anticipación': 'Com antecedência',
            'Público': 'Público',
            'Escolar y general': 'Escolar & geral',
            'Modalidad': 'Formato',
            'Presencial': 'Presencial',
            'Explora el museo': 'Explorar o museu',
            'Colección': 'Coleção',
            'Recorre la colección viva': 'Percorrer a coleção viva',
            'Historia Cómica': 'História Cômica',
            'Memoria del Circo y Clown': 'Memória do Circo e Clown',
            'El Museo': 'O Museu',
            'Sobre el Espacio': 'Sobre o Espaço',
            'Historia': 'História',
            'Memoria y origen': 'Memória e Origem',
            'Teatro escuela': 'Escola de Teatro',
            'Formación y mediación': 'Formação e Mediação',
            'Amigos de Teatromuseo': 'Amigos do Teatro-Museu',
            'Comunidad y apoyo': 'Comunidade e Apoio',
            'Cartelera': 'Programação',
            'Programación': 'Programação',
            'Detalle de cartelera': 'Detalhe da programação'
        }
    };

    const dict = dictionary[lang];
    if (dict) {
        const selector = '.menu-title__eyebrow, .menu-title__heading, .menu-title__copy, .content-panel__title, .content-panel__text, .stat-card__label, .stat-card__value, .menu-card__title, .month-group__title, .chip';
        const elementsToTranslate = document.querySelectorAll(selector);
        elementsToTranslate.forEach(el => {
            const originalText = el.textContent.trim();
            if (dict[originalText]) {
                el.textContent = dict[originalText];
            }
        });
    }
});
