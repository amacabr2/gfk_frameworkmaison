<?= $renderer->render('header'); ?>

    <div class="container">
        <h1>Bienvenue sur le blog</h1>

        <ul>
            <li><a href="<?= $router->generateUri('blog.show', ['slug' => 'azazaz']); ?>">Article 1</a></li>
            <li>Article 2</li>
            <li>Article 3</li>
        </ul>
    </div>

<?= $renderer->render('footer'); ?>