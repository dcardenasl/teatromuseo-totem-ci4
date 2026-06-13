// Configuración Centralizada del Tótem
const TOTEM_CONFIG = window.TOTEM_CONFIG || {
    // Fallback mínimo para que el archivo siga funcionando si se carga aislado.
    enableTransitions: false,
    enableAnimations: true
};

window.TOTEM_CONFIG = TOTEM_CONFIG;

// Registry of per-page cleanup functions to avoid memory leaks in SPA navigation.
window.__totemCleanup = window.__totemCleanup || [];

function runTotemCleanup() {
    if (Array.isArray(window.__totemCleanup)) {
        window.__totemCleanup.forEach((cleanup) => {
            if (typeof cleanup === 'function') {
                try {
                    cleanup();
                } catch (err) {
                    console.error('Error during totem cleanup:', err);
                }
            }
        });
        window.__totemCleanup = [];
    }

    if (typeof window.__totemSchoolPeopleModalCleanup === 'function') {
        try {
            window.__totemSchoolPeopleModalCleanup();
        } catch (err) {
            console.error('Error during school modal cleanup:', err);
        }
        window.__totemSchoolPeopleModalCleanup = null;
    }

    if (typeof window.totemSplashCleanup === 'function') {
        window.totemSplashCleanup();
    }
}

// Kiosk idle timer behavior and automatic reset to splash screen
let idleTime = 0;
const IDLE_LIMIT = 120; // 2 minutos en segundos
const WARN_AT = 105;    // Advertencia a los 105 segundos (15s antes del reset)
let warningShown = false;
let idleInterval = null;
const IDLE_ACTIVITY_EVENTS = ['mousedown', 'touchstart', 'keypress', 'pointerdown', 'mousemove', 'scroll', 'focusin'];
const SUPPORTED_LOCALES = ['es', 'en', 'fr', 'pt'];

function commitFetchedPage(htmlText, url, chosenTransition, x, y, isPopState) {
    runTotemCleanup();

    const parser = new DOMParser();
    const newDoc = parser.parseFromString(htmlText, 'text/html');
    const newStage = newDoc.querySelector('.totem-stage');

    if (!newStage) {
        window.location.href = url;
        return false;
    }

    document.querySelector('.totem-stage').innerHTML = newStage.innerHTML;
    document.title = newDoc.title;
    document.body.className = newDoc.body.className;

    if (!isPopState) {
        history.pushState({ transition: chosenTransition, x, y }, '', url);
    }

    const newScripts = newDoc.querySelectorAll('script');
    newScripts.forEach(oldScript => {
        if (oldScript.src && oldScript.src.includes('app.js')) return;
        const newScript = document.createElement('script');
        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
        newScript.textContent = oldScript.textContent;
        document.body.appendChild(newScript);
        newScript.remove();
    });

    updateActiveLanguageUI();
    applyLocalizedSystemMessages();
    setupIdleTimer();

    return true;
}

function launchLanguageSelection(url) {
    if (!TOTEM_CONFIG.enableAnimations || !TOTEM_CONFIG.enableTransitions) {
        window.location.href = url;
        return;
    }

    const stage = document.querySelector('.totem-stage');

    if (stage) {
        stage.classList.add('totem-stage--language-transition');
        stage.classList.add('totem-stage--language-leaving');
        stage.offsetHeight;
    }

    setTimeout(() => {
        window.location.href = url;
    }, 420);
}

function bindLanguageLaunchers() {
    document.addEventListener('click', (event) => {
        const splashLink = event.target.closest('a.splash-cta');
        const topbarLink = event.target.closest('a.pill-button--lang');

        if (topbarLink) {
            const href = topbarLink.getAttribute('href');
            if (!href || href.startsWith('javascript:') || href.startsWith('#')) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            window.location.href = href;
            return;
        }

        const link = splashLink;
        if (!link) {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('javascript:') || href.startsWith('#')) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        window.location.href = href;
    }, true);
}

// Determinar dinámicamente si estamos en la pantalla de bienvenida (splash)
function isSplashPage() {
    const path = window.location.pathname;
    return path === '/' || 
           path === '/index.php' || 
           path === '/index.php/' || 
           path.endsWith('/totem-ci4/') || 
           path.endsWith('/totem-ci4/index.php');
}

const idleOverlay = document.getElementById('idle-overlay');
const idleCount = document.getElementById('idle-count');

function getActiveTotemLocale() {
    const cookieMatch = document.cookie.match(/(?:^|;\s*)totem_lang=([^;]+)/);
    const cookieLocale = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
    const storedLocale = localStorage.getItem('totem_lang');
    const documentLocale = document.documentElement.lang;
    const candidate = cookieLocale || storedLocale || documentLocale || 'es';

    return SUPPORTED_LOCALES.includes(candidate) ? candidate : 'es';
}

function getSystemMessages(locale) {
    const messages = window.TOTEM_SYSTEM_MESSAGES || {};
    return messages[locale] || messages.es || null;
}

function applyLocalizedSystemMessages(locale = getActiveTotemLocale()) {
    const messages = getSystemMessages(locale);
    if (!messages) {
        return;
    }

    document.documentElement.lang = locale;

    const orientationTitle = document.querySelector('.orientation-warning__title');
    const orientationText = document.querySelector('.orientation-warning__text');
    const idleMessage = document.querySelector('.idle-overlay__msg');
    const idleButton = document.querySelector('.idle-overlay__card button.pill-button');

    if (orientationTitle) {
        orientationTitle.textContent = messages.rotateTitle;
    }

    if (orientationText) {
        orientationText.textContent = messages.rotateText;
    }

    if (idleMessage) {
        idleMessage.textContent = messages.idleMsg;
    }

    if (idleButton) {
        idleButton.textContent = messages.idleContinue;
    }
}

function showIdleWarning() {
    if (idleOverlay && !warningShown) {
        warningShown = true;
        idleOverlay.classList.remove('idle-overlay--hidden');
    }
}

function hideIdleWarning() {
    if (idleOverlay && warningShown) {
        warningShown = false;
        idleOverlay.classList.add('idle-overlay--hidden');
    }
}

function resetTimer(event) {
    if (warningShown && event && event.type === 'mousemove') {
        return;
    }
    idleTime = 0;
    hideIdleWarning();
}

function bindIdleListeners() {
    IDLE_ACTIVITY_EVENTS.forEach((eventName) => {
        document.removeEventListener(eventName, resetTimer);
        document.addEventListener(eventName, resetTimer, { passive: true });
    });
}

function unbindIdleListeners() {
    IDLE_ACTIVITY_EVENTS.forEach((eventName) => {
        document.removeEventListener(eventName, resetTimer);
    });
}

// Configurar o destruir dinámicamente el loop de inactividad
function setupIdleTimer() {
    // Destruir intervalo anterior para evitar duplicaciones
    if (idleInterval) {
        clearInterval(idleInterval);
        idleInterval = null;
    }

    if (!isSplashPage()) {
        // Enlazar controladores de actividad solo fuera del splash
        bindIdleListeners();

        idleTime = 0;
        idleInterval = setInterval(function() {
            idleTime++;
            
            if (idleTime >= WARN_AT && idleTime < IDLE_LIMIT) {
                showIdleWarning();
                if (idleCount) {
                    idleCount.textContent = IDLE_LIMIT - idleTime;
                }
            } else if (idleTime >= IDLE_LIMIT) {
                // Volver a splash usando navegación fluida
                hideIdleWarning();
                if (idleInterval) {
                    clearInterval(idleInterval);
                    idleInterval = null;
                }
                window.totemNavigateTo('/');
            }
        }, 1000);
    } else {
        // Si es Splash, remover escuchas y ocultar advertencia si estuviese abierta
        unbindIdleListeners();
        hideIdleWarning();
    }
}

// Inicializar dinámicamente el idioma activo en la barra superior
function updateActiveLanguageUI() {
    const langLabels = { es: 'ESP', en: 'ENG', fr: 'FRA', pt: 'POR' };
    const activeLang = getActiveTotemLocale();

    const langBtn = document.querySelector('.pill-button--lang span:last-child');
    if (langBtn) {
        langBtn.textContent = langLabels[activeLang] || 'ESP';
    }
}

// Inicializar comportamientos globales al cargar el DOM inicial
document.addEventListener('DOMContentLoaded', () => {
    // 0. Transición de Entrada (Apertura) - Solo si están habilitadas
    updateActiveLanguageUI();
    applyLocalizedSystemMessages();
    setupIdleTimer();
    bindLanguageLaunchers();
});


// =========================================================================
// MOTOR DE NAVEGACIÓN SPA ULTRA-SUAVE (INTERCAMBIO DE VISTAS POR AJAX)
// =========================================================================

let isNavigating = false;
let lastTouchX = '50%';
let lastTouchY = '50%';

// Capturar el último punto de contacto en la pantalla de forma global y robusta
document.addEventListener('pointerdown', (e) => {
    if (!TOTEM_CONFIG.enableTransitions) return;
    lastTouchX = e.clientX + 'px';
    lastTouchY = e.clientY + 'px';
}, { passive: true });

// Función para disparar la salpicadura de confeti dinámico
function triggerConfettiExplosion(container) {
    if (!container) return;
    container.innerHTML = '';
    const colors = ['#ffe600', '#ff0055', '#00ffcc', '#ff9900', '#0099ff', '#ff00ff', '#33ff33'];
    
    // Generar 80 papelitos para un estallido denso y espectacular!
    for (let i = 0; i < 80; i++) {
        const particle = document.createElement('div');
        particle.className = 'confetti-particle';
        
        // Formas aleatorias: 0=Círculo, 1=Cuadrado, 2=Cinta helical
        const shape = Math.floor(Math.random() * 3);
        if (shape === 0) {
            particle.style.borderRadius = '50%';
        } else if (shape === 1) {
            particle.style.borderRadius = '0%';
        } else {
            particle.style.width = '6px';
            particle.style.height = '24px';
            particle.style.borderRadius = '3px';
        }
        
        // Asignar color aleatorio
        particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        
        // Calcular trayectoria vectorial expansiva amplia (hasta 1150px) para cubrir todo el tótem
        const angle = Math.random() * Math.PI * 2;
        const distance = 150 + Math.random() * 1000; 
        const tx = Math.cos(angle) * distance + 'px';
        const ty = Math.sin(angle) * distance + 'px';
        const rotation = (Math.random() * 360) + 'deg';
        
        particle.style.setProperty('--tx', tx);
        particle.style.setProperty('--ty', ty);
        particle.style.setProperty('--rot', rotation);
        
        // Dimensionar ligeramente más grandes (12px a 30px) para máxima legibilidad
        const size = 12 + Math.random() * 18 + 'px';
        if (shape !== 2) {
            particle.style.width = size;
            particle.style.height = size;
        }
        
        // Inyectar animación de vuelo escalonada (1.5s total)
        particle.style.animation = 'confettiShoot 1.5s cubic-bezier(0.12, 1, 0.3, 1) forwards';
        
        container.appendChild(particle);
    }
}

window.totemNavigateTo = function(url, event = null, isPopState = false) {
    // Si las transiciones están desactivadas de forma centralizada, navegar clásicamente sin AJAX ni overlays
    if (!TOTEM_CONFIG.enableTransitions) {
        window.location.href = url;
        return;
    }

    if (isNavigating) return;
    isNavigating = true;

    const overlay = document.getElementById('totem-transition-overlay');
    if (!overlay) {
        window.location.href = url;
        isNavigating = false;
        return;
    }

    // Si estamos saliendo de la splash, cortar su rotador de idiomas antes de reemplazar la vista.
    if (document.querySelector('.splash-screen') && typeof window.totemSplashCleanup === 'function') {
        window.totemSplashCleanup();
    }

    // 1. Determinar el tipo de transición según el UX lúdico solicitado
    let chosenTransition = 'curtain'; // Por defecto, Cortina para navegación estructural y general

    // Identificar si la navegación se origina desde una tarjeta (card) o va a una sección de detalle/ficha
    const clickedElement = event ? event.target : null;
    const isCardClick = clickedElement && clickedElement.closest('.menu-card, .event-card, [class*="card"]');
    
    const isDetailUrl = url.includes('/detalle/') || 
                        url.includes('/fichas/') || 
                        url.includes('/titeres/') || 
                        url.includes('/mascaras/') || 
                        url.includes('/payasos/') || 
                        url.includes('/historia/') ||
                        url.includes('/historia-comica/');

    // Si es clic en tarjeta o destino de detalle, se elige aleatoriamente uno de los efectos lúdicos
    if (isCardClick || isDetailUrl) {
        // Reducimos la frecuencia de la mueca cómica ('grimace') para que sea la menos usada (~10% de probabilidad vs ~30% para las demás)
        const ludicTransitions = [
            'clown-nose', 'clown-nose', 'clown-nose',
            'confetti', 'confetti', 'confetti',
            'boxing-glove', 'boxing-glove', 'boxing-glove',
            'grimace'
        ];
        chosenTransition = ludicTransitions[Math.floor(Math.random() * ludicTransitions.length)];
    }

    // 2. Coordenadas X/Y obtenidas del evento o, como fallback garantizado, del último contacto táctil
    let x = lastTouchX;
    let y = lastTouchY;
    if (event) {
        const clientX = event.clientX || (event.touches && event.touches[0] && event.touches[0].clientX);
        const clientY = event.clientY || (event.touches && event.touches[0] && event.touches[0].clientY);
        if (clientX !== undefined && clientY !== undefined) {
            x = clientX + 'px';
            y = clientY + 'px';
        }
    }

    // 3. Activar overlay de transición en estado de salida (cerrándose)
    overlay.className = 'totem-transition-overlay';
    overlay.classList.add(`totem-transition--${chosenTransition}`);
    overlay.style.setProperty('--touch-x', x);
    overlay.style.setProperty('--touch-y', y);
    
    // Disparar confeti si corresponde
    if (chosenTransition === 'confetti') {
        const confettiContainer = overlay.querySelector('.totem-transition-overlay__confetti-container');
        triggerConfettiExplosion(confettiContainer);
    }
    
    // Forzar renderizado
    overlay.offsetHeight;
    overlay.classList.add('totem-transition-overlay--active');

    // 4. Cargar el contenido de la nueva página por AJAX (Fetch) en paralelo a la animación
    let animationTime = 600; // Tiempo por defecto para telón (600ms)
    if (chosenTransition === 'clown-nose' || chosenTransition === 'boxing-glove') {
        animationTime = 800; // 800ms para apreciar efectos más dinámicos
    } else if (chosenTransition === 'confetti') {
        animationTime = 375; // 375ms: Pico exacto del salto tras 375ms de acumulación y carga
    } else if (chosenTransition === 'grimace') {
        animationTime = 1150; // 1150ms para apreciar el ensamble escalonado de la mueca (900ms + 220ms delay)
    }

    const animationPromise = new Promise(resolve => setTimeout(resolve, animationTime));
    const fetchPromise = fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.text();
        });

    // 5. Esperar a que la animación y la carga de datos finalicen
    Promise.all([fetchPromise, animationPromise])
        .then(([htmlText]) => {
            if (!commitFetchedPage(htmlText, url, chosenTransition, x, y, isPopState)) {
                return;
            }

            // E) Ejecutar animación de Entrada (Apertura)
            overlay.classList.add('totem-transition-overlay--leaving');
            overlay.classList.remove('totem-transition-overlay--active');

            // Limpiar clases al terminar
            let leavingTime = 600;
            if (chosenTransition === 'clown-nose' || chosenTransition === 'grimace') {
                leavingTime = 800;
            } else if (chosenTransition === 'confetti') {
                leavingTime = 1200; // Más tiempo para que los confetis caigan del todo
            }

            setTimeout(() => {
                overlay.className = 'totem-transition-overlay';
                overlay.style.removeProperty('--touch-x');
                overlay.style.removeProperty('--touch-y');
                const confettiContainer = overlay.querySelector('.totem-transition-overlay__confetti-container');
                if (confettiContainer) confettiContainer.innerHTML = ''; // Limpiar confeti
                isNavigating = false;
            }, leavingTime);
        })
        .catch(err => {
            console.error('Error durante la navegación fluida:', err);
            // Fallback de seguridad: recargar la página clásicamente
            window.location.href = url;
            isNavigating = false;
        });
};

// Interceptar clics globales en enlaces locales para navegación fluida
document.addEventListener('click', (e) => {
    if (!TOTEM_CONFIG.enableTransitions) return;
    if (warningShown) return;

    const link = e.target.closest('a');
    if (link) {
        const href = link.getAttribute('href');
        
        if (href && 
            !href.startsWith('javascript:') && 
            !href.startsWith('#') && 
            !link.hasAttribute('download') && 
            link.target !== '_blank'
        ) {
            // Evitar interceptar dominios externos
            if (href.startsWith('http') && !href.includes(window.location.hostname)) {
                return;
            }
            e.preventDefault();
            window.totemNavigateTo(href, e);
        }
    }
});

// Soporte para los botones atrás/adelante nativos del sistema táctil
window.addEventListener('popstate', (event) => {
    const path = window.location.pathname + window.location.search;
    window.totemNavigateTo(path, null, true);
});

// Limpieza para evitar bloqueos por caché de navegación
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        const overlay = document.getElementById('totem-transition-overlay');
        if (overlay) {
            overlay.className = 'totem-transition-overlay';
        }
        isNavigating = false;
    }
});
