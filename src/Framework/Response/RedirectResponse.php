<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 17:39
 */

namespace Framework\Response;


use GuzzleHttp\Psr7\Response;

class RedirectResponse extends Response {

    /**
     * RedirectResponse constructor.
     * @param string $url
     */
    public function __construct(string $url) {
        parent::__construct(200, ['Location' => $url]);
    }
}