<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 12/10/17
 * Time: 14:13
 */

namespace Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfMiddleware implements MiddlewareInterface {

    /**
     * @var string
     */
    private $formKey;

    /**
     * @var string
     */
    private $sessionKey;
    
    /**
     * @var array
     */
    private $session;

    /**
     * @var int
     */
    private $limit;

    /**
     * CsrfMiddleware constructor.
     * @param $session
     * @param int $limit
     * @param string $formKey
     * @param string $sessionKey
     */
    public function __construct(&$session, int $limit = 50, string $formKey = '_csrf', string $sessionKey = 'csrf') {
        $this->validSession($session);
        $this->session = &$session;
        $this->limit = $limit;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
    }

    /**
     * @return string
     */
    public function getFormKey(): string {
        return $this->formKey;
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
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    return $delegate->process($request);
                } else {
                    $this->reject();
                }
            }
        }
        return $delegate->process($request);
    }

    /**
     * @return string
     */
    public function generateToken(): string {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }

    /**
     * @param $token
     */
    private function useToken($token): void {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }

    private function limitTokens(): void {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * @param $session
     * @throws \TypeError
     */
    private function validSession($session) {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError("La session passé au middleware CSRF n'est pas traitable comme un tableau");
        }
    }

    /**
     * @throws \Exception
     */
    private function reject() {
        throw new CsrfInvalidException();
    }

}