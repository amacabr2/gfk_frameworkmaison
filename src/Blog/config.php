<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 16/09/17
 * Time: 18:24
 */

use App\Blog\BlogWidget;
use function DI\add;
use function DI\get;
use function DI\object;

return [
    'blog.prefix' => '/blog',
    'admin.widgets' => add([
        get(BlogWidget::class)
    ])
];