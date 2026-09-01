<?php
/**
 * Shown by an async detail fetch (TOTEM-BFF-17) when the BFF confirms the
 * requested slug genuinely doesn't exist — the page shell already
 * responded 200 before this async call resolved, so a real HTTP 404 is no
 * longer possible; this renders the same copy a real 404 would show,
 * inline. Intentionally parameter-free, same reasoning as
 * `content_unavailable.php`'s docblock.
 */
?>
<section class="content-panel content-panel--soft" aria-label="<?= esc(lang('Common.error_404_label'), 'attr') ?>">
    <h2 class="content-panel__title"><?= esc(lang('Common.error_404_title')) ?></h2>
    <p class="content-panel__text"><?= esc(lang('Common.error_404_copy')) ?></p>
</section>
