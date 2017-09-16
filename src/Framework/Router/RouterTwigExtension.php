<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 16/09/17
 * Time: 20:16
 */

namespace Framework\Router;


use Framework\Router;

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
          new \Twig_SimpleFunction('path', [$this, 'pathFor'])
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

}