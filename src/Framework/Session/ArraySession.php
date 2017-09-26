<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 26/09/17
 * Time: 14:58
 */

namespace Framework\Session;


class ArraySession implements SessionInterface {

    private $session = [];

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null) {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value) {
        $this->session[$key] = $value;
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key) {
        unset($this->session[$key]);
    }

}