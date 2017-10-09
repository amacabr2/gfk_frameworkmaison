<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 16/09/17
 * Time: 20:16
 */

namespace Framework\Router;


use Framework\Router;
use Twig_SimpleFunction;

class RouterTwigExtension extends \Twig_Extension {
    /**
     * @var Router
     */
    private $router;

    /**
     * RouterTwigExtension constructor.
     * @param Router $router
     */
    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions(): array {
        return [
            new Twig_SimpleFunction('path', [$this, 'pathFor']),
            new Twig_SimpleFunction('is_subpath', [$this, 'isSubpath'])
        ];
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function pathFor(string $path, array $params = []): string {
        return $this->router->generateUri($path, $params);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isSubpath(string $path): bool {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($path);
        return strpos($uri, $expectedUri) !== false;
    }

}