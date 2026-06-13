/**
 * Collection main page - scroll restoration
 * Keeps this screen anchored to the top to reproduce the PDF reading order.
 */
(function() {
    window.history.scrollRestoration = 'manual';

    function scrollToTop() {
        window.scrollTo(0, 0);
    }

    window.addEventListener('load', scrollToTop, { once: true });
    window.addEventListener('pageshow', scrollToTop);
})();
