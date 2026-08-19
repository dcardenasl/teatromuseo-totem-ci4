<?php
/**
 * @var array{tags?:array, title:string, venue?:string, image?:string, ...}|null $detail
 * @var array $nav
 * @var bool $unavailable
 * @var bool $stale
 * @var bool $deferred whether no cache was available synchronously — the
 *     shell renders immediately with a loading placeholder, and a small
 *     script fetches the real content from $dataUrl right after paint
 *     (TOTEM-BFF-17: progressive hydration for cold-cache detail pages,
 *     which the background warm-up command deliberately never covers).
 * @var string|null $dataUrl
 */
$deferred = $deferred ?? false;
$dataUrl = $dataUrl ?? '';
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div
            class="screen-page__body billboard-detail"
            data-billboard-detail-root
            <?= $deferred ? 'data-async-detail-url="' . esc($dataUrl, 'attr') . '"' : '' ?>
        >
        <?php if ($deferred): ?>
            <section class="content-panel content-panel--soft" data-async-detail-skeleton aria-live="polite" aria-label="<?= esc(lang('Common.content_loading_label'), 'attr') ?>">
                <p class="content-panel__text"><?= esc(lang('Common.content_loading_label')) ?></p>
            </section>
        <?php elseif ($unavailable || $detail === null): ?>
            <?= view('totem/partials/content_unavailable') ?>
        <?php else: ?>
            <?= view('totem/partials/billboard_detail_content', ['detail' => $detail, 'stale' => $stale ?? false]) ?>
        <?php endif; ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Menu.programming'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>

<script>
(function() {
    function initBillboardSlider() {
        const slider = document.querySelector('[data-billboard-slider]');
        const nav = document.querySelector('.billboard-detail__nav');
        if (!slider || !nav) return;

        const prevBtn = nav.querySelector('[data-slide-prev]');
        const nextBtn = nav.querySelector('[data-slide-next]');
        const slides = slider.querySelectorAll('[data-slide-index]');
        const totalSlides = slides.length;
        if (totalSlides <= 1) return;

        let currentIndex = 0;

        function showSlide(index) {
            slides.forEach(slide => {
                const slideIdx = parseInt(slide.getAttribute('data-slide-index'), 10);
                slide.style.display = slideIdx === index ? 'block' : 'none';
            });
            currentIndex = index;
        }

        prevBtn.addEventListener('click', () => {
            let nextIndex = currentIndex - 1;
            if (nextIndex < 0) nextIndex = totalSlides - 1;
            showSlide(nextIndex);
        });

        nextBtn.addEventListener('click', () => {
            let nextIndex = currentIndex + 1;
            if (nextIndex >= totalSlides) nextIndex = 0;
            showSlide(nextIndex);
        });
    }

    window.TotemInitBillboardSlider = initBillboardSlider;
    initBillboardSlider();

    const root = document.querySelector('[data-async-detail-url]');
    if (!root) return;

    var unavailableHtml = <?= json_encode(view('totem/partials/content_unavailable')) ?>;
    var notFoundHtml = <?= json_encode(view('totem/partials/content_not_found')) ?>;

    fetch(root.getAttribute('data-async-detail-url'), { headers: { 'Accept': 'application/json' } })
        .then(function (response) { return response.json(); })
        .then(function (payload) {
            if (payload && payload.state === 'not_found') {
                root.innerHTML = notFoundHtml;
            } else if (payload && typeof payload.html === 'string') {
                root.innerHTML = payload.html;
                root.removeAttribute('data-async-detail-url');
                initBillboardSlider();
            } else {
                root.innerHTML = unavailableHtml;
            }
        })
        .catch(function () {
            root.innerHTML = unavailableHtml;
        });
})();
</script>
<?= $this->endSection() ?>
