<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 10/10/17
 * Time: 10:06
 */

namespace Framework\Middleware;


use Psr\Http\Message\ServerRequestInterface;

class MethodMiddleware {

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return \GuzzleHttp\Psr7\MessageTrait
     */
    function __invoke(ServerRequestInterface $request, callable $next) {
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_method', $parsedBody) and in_array($parsedBody['_method'], ['DELETE', 'PUT'])) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        return $next($request);
    }
}