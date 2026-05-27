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
