<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 18:34
 */

namespace App\Auth;


use Framework\AuthInterface;

class AuthTwigExtension extends \Twig_Extension {

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * AuthTwigExtension constructor.
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth) {
        $this->auth = $auth;
    }


    /**
     * @return array
     */
    public function getFunctions(): array {
        return [
            new \Twig_SimpleFunction('current_user', [$this->auth, 'getUser'])
        ];
    }

}