<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 26/09/17
 * Time: 14:28
 */

namespace Framework\Session;


class FlashService {

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKey = 'flash';

    /**
     * @var string
     */
    private $messages;

    /**
     * FlashService constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session) {
        $this->session = $session;
        $this->messages = null;
    }

    /**
     * @param string $message
     */
    public function success(string $message) {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function error(string $message) {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    /**
     * @param string $type
     * @return null|string
     */
    public function get(string $type): ?string {
        if (is_null($this->messages)) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }
        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }
        return null;
    }

}