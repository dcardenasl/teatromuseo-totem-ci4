// Configuración Centralizada del Tótem
const TOTEM_CONFIG = {
    // Cambia a 'false' para desactivar por completo todas las transiciones lúdicas y swaps de AJAX globales.
    // También se puede sobreescribir temporalmente en el navegador usando el parámetro '?transitions=0' en la URL
    enableTransitions: (new URLSearchParams(window.location.search).get('transitions') !== '0') && true
};

// Kiosk idle timer behavior and automatic reset to splash screen
let idleTime = 0;
const IDLE_LIMIT = 120; // 2 minutos en segundos
const WARN_AT = 105;    // Advertencia a los 105 segundos (15s antes del reset)
let warningShown = false;
let idleInterval = null;

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

// Configurar o destruir dinámicamente el loop de inactividad
function setupIdleTimer() {
    // Destruir intervalo anterior para evitar duplicaciones
    if (idleInterval) {
        clearInterval(idleInterval);
        idleInterval = null;
    }

    if (!isSplashPage()) {
        // Enlazar controladores de actividad
        ['mousedown', 'touchstart', 'keypress', 'pointerdown', 'mousemove', 'scroll', 'focusin'].forEach((eventName) => {
            document.removeEventListener(eventName, resetTimer);
            document.addEventListener(eventName, resetTimer, { passive: true });
        });

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
        hideIdleWarning();
    }
}

// Inicializar dinámicamente el idioma activo en la barra superior
function updateActiveLanguageUI() {
    const langLabels = { es: 'ESP', en: 'ENG', fr: 'FRA', pt: 'POR' };
    const cookieMatch = document.cookie.match(/totem_lang=([^;]+)/);
    const activeLang = (cookieMatch ? cookieMatch[1] : null)
        || localStorage.getItem('totem_lang')
        || 'es';

    const langBtn = document.querySelector('.pill-button--lang span:last-child');
    if (langBtn) {
        langBtn.textContent = langLabels[activeLang] || 'ESP';
    }
}

// Inicializar comportamientos globales al cargar el DOM inicial
document.addEventListener('DOMContentLoaded', () => {
    // 0. Transición de Entrada (Apertura) - Solo si están habilitadas
    if (TOTEM_CONFIG.enableTransitions) {
        const overlay = document.getElementById('totem-transition-overlay');
        if (overlay) {
            const savedTransition = sessionStorage.getItem('totem_active_transition');
            const touchX = sessionStorage.getItem('totem_transition_x') || '50%';
            const touchY = sessionStorage.getItem('totem_transition_y') || '50%';

            if (savedTransition) {
                overlay.classList.add(`totem-transition--${savedTransition}`);
                overlay.style.setProperty('--touch-x', touchX);
                overlay.style.setProperty('--touch-y', touchY);
                overlay.classList.add('totem-transition-overlay--active');
                
                requestAnimationFrame(() => {
                    overlay.classList.add('totem-transition-overlay--leaving');
                    overlay.classList.remove('totem-transition-overlay--active');
                    
                    setTimeout(() => {
                        overlay.className = 'totem-transition-overlay';
                        overlay.style.removeProperty('--touch-x');
                        overlay.style.removeProperty('--touch-y');
                    }, 600);
                });
            }
            sessionStorage.removeItem('totem_active_transition');
            sessionStorage.removeItem('totem_transition_x');
            sessionStorage.removeItem('totem_transition_y');
        }
    }

    updateActiveLanguageUI();
    setupIdleTimer();
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
            // Parsear el HTML descargado
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(htmlText, 'text/html');
            const newStage = newDoc.querySelector('.totem-stage');

            if (!newStage) {
                // Fallback clásico si la estructura no coincide
                window.location.href = url;
                return;
            }

            // A) Reemplazar el contenedor escénico principal
            document.querySelector('.totem-stage').innerHTML = newStage.innerHTML;

            // B) Actualizar Metadatos del Documento
            document.title = newDoc.title;
            document.body.className = newDoc.body.className;

            // C) Actualizar URL en el historial del navegador si no es un popstate
            if (!isPopState) {
                history.pushState({ transition: chosenTransition, x, y }, '', url);
            }

            // D) Re-escanear y ejecutar scripts específicos de la nueva página
            const newScripts = newDoc.querySelectorAll('script');
            newScripts.forEach(oldScript => {
                if (oldScript.src && oldScript.src.includes('app.js')) return; // Evitar cargar este script nuevamente
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
                newScript.remove(); // Limpiar el DOM tras ejecutarse
            });

            // E) Re-inicializar componentes globales
            updateActiveLanguageUI();
            setupIdleTimer();

            // F) Ejecutar animación de Entrada (Apertura)
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
