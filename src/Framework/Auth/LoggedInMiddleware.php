<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 27/10/17
 * Time: 13:04
 */

namespace Framework\Auth;


use Framework\AuthInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoggedInMiddleware implements MiddlewareInterface {

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * LoggedInMiddleware constructor.
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth){
        $this->auth = $auth;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface {
        $user = $this->auth->getUser();
        if (is_null($user)) {
            throw new ForbiddenException();
        }
        return $delegate->process($request->withAttribute('user', $user));
    }
}