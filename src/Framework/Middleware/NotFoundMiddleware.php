<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 10/10/17
 * Time: 10:27
 */

namespace Framework\Middleware;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundMiddleware {

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return \GuzzleHttp\Psr7\MessageTrait
     */
    function __invoke(ServerRequestInterface $request, callable $next) {
        return new Response(404, [], 'Erreur 404');
    }

}