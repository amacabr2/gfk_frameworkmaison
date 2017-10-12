<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 10/10/17
 * Time: 10:37
 */

namespace Framework\Middleware;


use Framework\Router\Route;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DispatcherMiddleware {

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DispatcherMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return \GuzzleHttp\Psr7\MessageTrait
     * @throws \Exception
     */
    function __invoke(ServerRequestInterface $request, callable $next) {
        $route = $request->getAttribute(Route::class);
        if (is_null($route)) {
            return $next($request);
        }
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }

}