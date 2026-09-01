<?php
/**
 * Shown instead of a screen's normal content when the BFF could not be
 * reached and no previously-cached real answer exists either — never
 * invented data standing in for the real thing.
 *
 * Intentionally takes no parameters: CI4's `view()` helper persists a
 * template's data across sibling calls within the same request
 * (`Config\View::$saveData`), so a version of this partial that read an
 * optional `$title`/`$copy` override was silently picking up leftover
 * variables from an unrelated, earlier `view()` call in the same request
 * instead of its own default copy. For the one real use case that needs
 * different copy (a confirmed-404 reported by an async detail fetch), use
 * `content_not_found.php` instead — a separate, equally parameter-free
 * partial — rather than parameterizing this one again.
 */
?>
<section class="content-panel content-panel--soft" aria-label="<?= esc(lang('Common.content_unavailable_label'), 'attr') ?>">
    <h2 class="content-panel__title"><?= esc(lang('Common.content_unavailable_title')) ?></h2>
    <p class="content-panel__text"><?= esc(lang('Common.content_unavailable_copy')) ?></p>
</section>
