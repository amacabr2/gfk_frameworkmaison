<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 16/09/17
 * Time: 17:56
 */

use function DI\factory;
use function DI\get;
use function DI\object;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;

return [
    'database.adapter' => 'mysql',
    'database.host' => 'localhost',
    'database.username' => 'amacabr2',
    'database.password' => 'sub@bg10',
    'database.name' => 'gfk_frameworkmaison',
    'views.path' => dirname(__DIR__) . '/src/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class)
    ],
    Router::class => object(),
    RendererInterface::class => factory(TwigRendererFactory::class)
];