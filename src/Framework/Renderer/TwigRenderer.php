<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 15/09/17
 * Time: 17:08
 */

namespace Framework\Renderer;


use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigRenderer implements RendererInterface {

    private $twig;

    private $loader;

    /**
     * TwigRenderer constructor.
     * @param string $path
     */
    public function __construct(string $path) {
        $this->loader = new Twig_Loader_Filesystem($path);
        $this->twig = new Twig_Environment($this->loader, []);
    }

    /**
     * Add path for load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Allows to render a view
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string {
        return $this->twig->render($view . '.html.twig', $params);
    }

    /**
     * Predicts to add global variables from the beginning
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void {
        $this->twig->addGlobal($key, $value);
    }

}