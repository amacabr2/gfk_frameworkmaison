<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 26/09/17
 * Time: 14:13
 */

namespace Framework\Session;


interface SessionInterface {

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key);

}