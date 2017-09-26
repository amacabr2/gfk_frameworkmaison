<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 26/09/17
 * Time: 14:15
 */

namespace Framework\Session;


class PHPSession implements SessionInterface {

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null) {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value) {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key) {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    private function ensureStarted() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

}