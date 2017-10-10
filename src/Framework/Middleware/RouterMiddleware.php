<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 10/10/17
 * Time: 10:30
 */

namespace Framework\Middleware;


use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddleware {

    /**
     * @var Router
     */
    private $router;

    /**
     * RouterMiddleware constructor.
     * @param Router $router
     */
    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return \GuzzleHttp\Psr7\MessageTrait
     */
    function __invoke(ServerRequestInterface $request, callable $next) {
        $route = $this->router->match($request);
        if (is_null($route)) {
            return $next($request);
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }

}