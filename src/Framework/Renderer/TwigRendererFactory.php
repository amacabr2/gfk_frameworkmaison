<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 16/09/17
 * Time: 18:10
 */

namespace Framework\Renderer;

use Psr\Container\ContainerInterface;

class TwigRendererFactory {

    /**
     * @param ContainerInterface $container
     * @return TwigRenderer
     */
    public function __invoke(ContainerInterface $container): TwigRenderer {
        $viewPath = $container->get('views.path');
        $loader = new \Twig_Loader_Filesystem($viewPath);
        $twig = new \Twig_Environment($loader);
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($twig);
    }

}