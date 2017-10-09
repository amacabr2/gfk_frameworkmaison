<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 09/10/17
 * Time: 10:58
 */

namespace App\Admin\Controllers;


use App\Admin\AdminWidgetInterface;
use Framework\Renderer\RendererInterface;

class DashboardController {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var AdminWidgetInterface[]
     */
    private $widgets;

    /**
     * DashboardController constructor.
     * @param RendererInterface $renderer
     * @param AdminWidgetInterface[] $widgets
     */
    public function __construct(RendererInterface $renderer, array $widgets) {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    /**
     * @return string
     */
    public function __invoke() {
        $widgets = array_reduce($this->widgets, function(string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }

}