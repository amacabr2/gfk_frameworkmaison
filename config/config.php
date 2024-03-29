<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 16/09/17
 * Time: 17:56
 */

use function DI\env;
use function DI\factory;
use function DI\get;
use function DI\object;
use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Twig\CsrfTwigExtension;
use Framework\Twig\FlashExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\PagerFantaExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;

use Psr\Container\ContainerInterface;

return [
    'env' => env('ENV', 'production'),
    'database.adapter' => 'mysql',
    'database.host' => 'localhost',
    'database.username' => 'amacabr2',
    'database.password' => 'sub@bg10',
    'database.name' => 'gfk_frameworkmaison',
    'views.path' => dirname(__DIR__) . '/src/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
        get(PagerFantaExtension::class),
        get(TextExtension::class),
        get(TimeExtension::class),
        get(FlashExtension::class),
        get(FormExtension::class),
        get(CsrfTwigExtension::class),
    ],
    SessionInterface::class => object(PHPSession::class),
    CsrfMiddleware::class => object()->constructor(get(SessionInterface::class)),
    Router::class => factory(Router\RouterFactory::class),
    RendererInterface::class => factory(TwigRendererFactory::class),
    PDO::class => function(ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];