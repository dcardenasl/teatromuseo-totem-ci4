<?php
/**
 * @var string $title
 * @var string $intro
 * @var string $gridClass
 * @var array<int, array{title:string, href:string, image:string, copy?:string, tone?:string}> $items
 * @var array<int, array{label:string, href:string, active?:bool, disabled?:bool}> $tabs
 * @var string $footer
 */

$title = $title ?? '';
$intro = $intro ?? '';
$gridClass = trim('collection-grid ' . ($gridClass ?? ''));
$items = $items ?? [];
$tabs = $tabs ?? [];
$footer = $footer ?? '';
?>

<section class="collection-grid-layout">
    <header class="collection-grid-layout__header">
        <?php if ($intro !== ''): ?>
            <p class="collection-grid-layout__intro"><?= esc($intro) ?></p>
        <?php endif; ?>

        <?= view('totem/partials/collection_section_nav', ['tabs' => $tabs]) ?>
    </header>

    <div class="<?= esc($gridClass) ?>" data-collection-grid>
        <?php foreach ($items as $index => $item): ?>
            <?php 
            $pageNum = (int) floor($index / 8); 
            $tone = trim('collection-card--' . ($item['tone'] ?? 'coral')); 
            ?>
            <a class="collection-card <?= esc($tone) ?>" href="<?= esc(base_url($item['href'] ?? '#'), 'attr') ?>" data-page="<?= $pageNum ?>" style="display: <?= $pageNum === 0 ? 'grid' : 'none' ?>;">
                <div class="collection-card__media" aria-hidden="true">
                    <img
                        class="collection-card__image"
                        src="<?= esc(base_url($item['image'] ?? ''), 'attr') ?>"
                        alt=""
                        loading="lazy"
                    >
                </div>
                <div class="collection-card__body">
                    <h3 class="collection-card__title"><?= esc($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['copy'])): ?>
                        <p class="collection-card__copy"><?= esc($item['copy']) ?></p>
                    <?php endif; ?>
                    <span class="collection-card__cta"><?= esc(lang('Collection.card_cta')) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php 
    $totalPages = (int) ceil(count($items) / 8);
    if ($totalPages > 1): 
    ?>
        <div class="collection-grid-pagination" data-total-pages="<?= $totalPages ?>" data-current-page="0">
            <button type="button" class="pill-button pill-button--secondary" data-page-prev disabled>
                <?= esc(lang('Nav.back') ?? 'Anterior') ?>
            </button>
            <span class="collection-grid-pagination__info">1 / <?= $totalPages ?></span>
            <button type="button" class="pill-button pill-button--secondary" data-page-next>
                <?= esc(lang('Collection.card_cta') ?? 'Siguiente') ?>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($footer !== ''): ?>
        <p class="collection-grid-layout__footer"><?= esc($footer) ?></p>
    <?php endif; ?>
</section>

<style>
.collection-grid-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 24px;
    width: 100%;
}
.collection-grid-pagination__info {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: 0.08em;
}
.collection-grid-pagination button[disabled] {
    opacity: 0.4;
    pointer-events: none;
}
</style>

<script>
(function() {
    const grid = document.querySelector('[data-collection-grid]');
    const pagination = document.querySelector('.collection-grid-pagination');
    if (!grid || !pagination) return;

    const prevBtn = pagination.querySelector('[data-page-prev]');
    const nextBtn = pagination.querySelector('[data-page-next]');
    const infoSpan = pagination.querySelector('.collection-grid-pagination__info');

    let currentPage = 0;
    const totalPages = parseInt(pagination.getAttribute('data-total-pages'), 10);

    function showPage(page) {
        const cards = grid.querySelectorAll('.collection-card');
        cards.forEach(card => {
            const cardPage = parseInt(card.getAttribute('data-page'), 10);
            if (cardPage === page) {
                card.style.display = 'grid';
            } else {
                card.style.display = 'none';
            }
        });

        currentPage = page;
        pagination.setAttribute('data-current-page', currentPage);
        infoSpan.textContent = `${currentPage + 1} / ${totalPages}`;

        prevBtn.toggleAttribute('disabled', currentPage === 0);
        nextBtn.toggleAttribute('disabled', currentPage === totalPages - 1);
    }

    prevBtn.addEventListener('click', () => {
        if (currentPage > 0) {
            showPage(currentPage - 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentPage < totalPages - 1) {
            showPage(currentPage + 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
})();
</script>
