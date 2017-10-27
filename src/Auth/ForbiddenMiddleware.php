<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 18:10
 */

namespace App\Auth;


use App\Auth\Entity\User;
use Framework\Auth\ForbiddenException;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ForbiddenMiddleware implements MiddlewareInterface {

    /**
     * @var string
     */
    private $loginPath;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ForbiddenMiddleware constructor.
     * @param string $loginPath
     * @param SessionInterface $session
     */
    public function __construct(string $loginPath, SessionInterface $session) {
        $this->loginPath = $loginPath;
        $this->session = $session;
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
    public function process(ServerRequestInterface $request, DelegateInterface $delegate) {
        try {
            return $delegate->process($request);
        } catch (ForbiddenException $exception) {
            return $this->redirectLogin($request);
        } catch (\TypeError $error) {
            if (strpos($error->getMessage(), User::class) !== false) {
                return$this->redirectLogin($request);
            }
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return RedirectResponse
     */
    public function redirectLogin(ServerRequestInterface $request): RedirectResponse {
        $this->session->set('auth.redirect', $request->getUri()->getPath());
        (new FlashService($this->session))->error('Vouse devez possèder un compte pour accèder à cette page');
        return new RedirectResponse($this->loginPath);
    }
}