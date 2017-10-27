<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 14:03
 */

namespace App\Auth\Controllers;


use App\Auth\DatabaseAuth;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginController {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var DatabaseAuth
     */
    private $auth;

    /**
     * @var Router
     */
    private $router;
    /**
     * @var FlashService
     */
    private $service;

    use RouterAwareAction;

    /**
     * LoginController constructor.
     * @param RendererInterface $renderer
     * @param DatabaseAuth $auth
     * @param FlashService $service
     * @param Router $router
     */
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, FlashService $service, Router $router) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->router = $router;
        $this->service = $service;
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request) {
        if ($request->getMethod() === "GET") {
            return $this->login();
        } else {
            return $this->attempt($request);
        }
    }

    /**
     * @return string
     */
    public function login(): string {
        return $this->renderer->render('@auth/login');
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function attempt(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);
        if ($user) {
            $this->service->success('Bienvenue ' . $params['username']);
            return $this->redirect('admin');
        } else {
            $this->service->error('Identifiant ou mot de passe incorrecte');
            return $this->redirect('auth.login');
        }
    }

}