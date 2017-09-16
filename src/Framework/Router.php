<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 14/09/17
 * Time: 11:27
 */

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 * Register a math route
 * @package Framework
 */
class Router {

    private $router;

    public function __construct() {
        $this->router = new FastRouteRouter();
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function get(string $path, $callable, string $name) {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route {
        $result = $this->router->match($request);
        if ($result->isSuccess()) {
            return new Route($result->getMatchedRouteName(), $result->getMatchedMiddleware(), $result->getMatchedParams());
        }
        return null;
    }

    /**
     * @return FastRouteRouter
     */
    public function getRouter(): FastRouteRouter {
        return $this->router;
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return null|string
     */
    public function generateUri(string $name, array $parameters): ?string {
        return $this->router->generateUri($name, $parameters);
    }

}