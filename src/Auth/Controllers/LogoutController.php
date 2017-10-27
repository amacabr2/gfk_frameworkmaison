<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 17:34
 */

namespace App\Auth\Controllers;


use App\Auth\DatabaseAuth;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;

class LogoutController {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var DatabaseAuth
     */
    private $auth;

    /**
     * @var FlashService
     */
    private $service;

    use RouterAwareAction;

    /**
     * LogoutController constructor.
     * @param RendererInterface $renderer
     * @param DatabaseAuth $auth
     * @param FlashService $service
     */
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, FlashService $service) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->service = $service;
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(): ResponseInterface {
        $this->auth->logout();
        $this->service->success("Vous êtes déconnecté");
        return new RedirectResponse('/blog');
    }
}