<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 13:37
 */

namespace Framework\Middleware;


use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouterPrefixedMiddleware implements MiddlewareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string
     */
    private $middleware;

    /**
     * RouterPrefixedMiddleware constructor.
     * @param ContainerInterface $container
     * @param string $prefix
     * @param string $middleware
     */
    public function __construct(ContainerInterface $container, string $prefix, string $middleware) {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->middleware = $middleware;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface {
        $path = $request->getUri()->getPath();
        if (strpos($path, $this->prefix) === 0) {
           return $this->container->get($this->middleware)->process($request, $delegate);
        }
        return $delegate->process($request);
    }
}