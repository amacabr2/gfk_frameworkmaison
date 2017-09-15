<?= $renderer->render('header', ['title' => $slug]); ?>

    <div class="container">
        Bienvenue sur l'article <?= $slug ?>
    </div>

<?= $renderer->render('footer') ?>
