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

    public function __construct(?string $cache = null) {
        $this->router = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => !is_null($cache),
            FastRouteRouter::CONFIG_CACHE_FILE => $cache
        ]);
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function get(string $path, $callable, ?string $name = null) {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function post(string $path, $callable, ?string $name = null) {
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }

    public function delete(string $path, $callable, ?string $name = null) {
        $this->router->addRoute(new ZendRoute($path, $callable, ['DELETE'], $name));
    }

    /**
     * @param string $prefixPath
     * @param $callable
     * @param null|string $prefixName
     */
    public function crud(string $prefixPath, $callable, ?string $prefixName) {
        $this->get($prefixPath, $callable, "$prefixName.index");
        $this->get($prefixPath . '/new', $callable, "$prefixName.create");
        $this->post($prefixPath . '/new', $callable);
        $this->get($prefixPath . '/{id:\d+}', $callable, "$prefixName.edit");
        $this->post($prefixPath . '/{id:\d+}', $callable);
        $this->delete($prefixPath . '/{id:\d+}', $callable, "$prefixName.delete");
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
     * @param array $params
     * @param array $queryParams
     * @return null|string
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string {
        $uri = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }

}