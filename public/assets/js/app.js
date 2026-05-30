// Kiosk idle timer behavior and automatic reset to splash screen
let idleTime = 0;
const IDLE_LIMIT = 120; // 2 minutos en segundos
const WARN_AT = 105;    // Advertencia a los 105 segundos (15s antes del reset)
let warningShown = false;

const isSplash = window.location.pathname === '/' ||
                 window.location.pathname === '/index.php/' ||
                 window.location.pathname === '/index.php';

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

// Inicializar comportamientos globales al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    // 1. Dinamismo del Botón de Idioma Activo en la Topbar
    const langLabels = { es: 'ESP', en: 'ENG', fr: 'FRA', pt: 'POR' };
    
    // Obtener idioma activo desde la cookie o el localStorage
    const cookieMatch = document.cookie.match(/totem_lang=([^;]+)/);
    const activeLang = (cookieMatch ? cookieMatch[1] : null)
        || localStorage.getItem('totem_lang')
        || 'es';

    const langBtn = document.querySelector('.pill-button--lang span:last-child');
    if (langBtn) {
        langBtn.textContent = langLabels[activeLang] || 'ESP';
    }

    // 2. Controladores de interacción para resetear el temporizador de inactividad
    if (!isSplash) {
        ['mousedown', 'touchstart', 'keypress', 'pointerdown', 'mousemove', 'scroll', 'focusin'].forEach((eventName) => {
            document.addEventListener(eventName, resetTimer, { passive: true });
        });

        // Loop de inactividad ejecutándose segundo a segundo
        setInterval(function() {
            idleTime++;
            
            if (idleTime >= WARN_AT && idleTime < IDLE_LIMIT) {
                showIdleWarning();
                if (idleCount) {
                    idleCount.textContent = IDLE_LIMIT - idleTime;
                }
            } else if (idleTime >= IDLE_LIMIT) {
                window.location.href = '/';
            }
        }, 1000);
    }
});
