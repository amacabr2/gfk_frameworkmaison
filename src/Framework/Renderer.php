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

    private $paths = [];

    /**
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
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string {
        $path = $this->hasNamespace($view) ? $this->replaceNamespace($view) . '.php' : $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        ob_start();
        extract($params);
        require($path);
        return ob_get_clean();
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