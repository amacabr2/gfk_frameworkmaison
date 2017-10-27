<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 15/09/17
 * Time: 17:02
 */

namespace Framework\Renderer;

interface RendererInterface {

    /**
     * Add path for load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Allows to render a view
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Predicts to add global variables from the beginning
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void;
}