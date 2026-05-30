<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <section class="detail-layout">
                <article class="detail-main">
                    <div class="detail-meta">
                        <?php foreach ($detail['tags'] as $tag): ?>
                            <span class="chip"><?= esc($tag) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <p class="detail-main__lead">
                        <strong><?= esc($detail['company']) ?></strong><br>
                        <?= esc($detail['direction']) ?>
                    </p>

                    <div class="detail-hero__image detail-hero__image--compact" aria-hidden="true"></div>

                    <p class="content-panel__text"><?= esc($detail['copy']) ?></p>
                </article>

                <aside class="detail-sidebar">
                    <div class="detail-stat">
                        <span class="detail-stat__label"><?= esc($detail['date']) ?></span>
                        <span class="detail-stat__value"><?= esc($detail['time']) ?></span>
                    </div>
                    <div class="detail-stat">
                        <span class="detail-stat__label">Duración aproximada</span>
                        <span class="detail-stat__value"><?= esc($detail['duration']) ?></span>
                    </div>
                    <div class="detail-stat">
                        <span class="detail-stat__label"><?= esc($detail['price']) ?></span>
                        <span class="detail-stat__value">Niños, estudiantes y 3 edad: $3.500</span>
                    </div>

                    <p class="detail-sidebar__note">*Las entradas se adquieren 20 minutos antes de la función en la boletería de Teatromuseo.</p>
                </aside>
            </section>
        </div>

        <?= $this->include('totem/partials/page_footer', ['variant' => 'detail']) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => esc($detail['title']),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
