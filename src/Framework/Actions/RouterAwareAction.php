<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 18/09/17
 * Time: 11:26
 */

namespace Framework\Actions;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RouterAwareAction
 * @package Framework\Actions
 */
trait RouterAwareAction {

    /**
     * @param string $path
     * @param array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface {
        $redirectUri = $this->router->generateUri($path, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }

}