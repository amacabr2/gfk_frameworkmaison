<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 15/09/17
 * Time: 09:35
 */

namespace Framework;


class Renderer {

    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * @var array
     */
    private $paths = [];

    /**
     * @var array
     */
    private $globals = [];

    /**
     * Add path for load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Allows to render a view
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string {
        $path = $this->hasNamespace($view) ? $this->replaceNamespace($view) . '.php' : $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

    /**
     * Predicts to add global variables from the beginning
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void {
        $this->globals[$key] = $value;
    }

    /**
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool  {
        return $view[0] === '@';
    }

    /**
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string {
        return substr($view, 1, strpos($view, '/') - 1) ;
    }

    /**
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string  {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }

}