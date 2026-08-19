<?php
/**
 * Shown instead of a screen's normal content when the BFF could not be
 * reached and no previously-cached real answer exists either — never
 * invented data standing in for the real thing.
 */
?>
<section class="content-panel content-panel--soft" aria-label="<?= esc(lang('Common.content_unavailable_label'), 'attr') ?>">
    <h2 class="content-panel__title"><?= esc(lang('Common.content_unavailable_title')) ?></h2>
    <p class="content-panel__text"><?= esc(lang('Common.content_unavailable_copy')) ?></p>
</section>
