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
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
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
     * @var SessionInterface
     */
    private $session;

    use RouterAwareAction;

    /**
     * LoginController constructor.
     * @param RendererInterface $renderer
     * @param DatabaseAuth $auth
     * @param Router $router
     * @param SessionInterface $session
     */
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, Router $router, SessionInterface $session) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
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
            (new FlashService($this->session))->success('Bienvenue ' . $params['username']);
            $path = $this->session->get('auth.redirect') ?: $this->router->generateUri('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrecte');
            return $this->redirect('auth.login');
        }
    }

}