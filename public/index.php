<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 13/09/17
 * Time: 09:43
 */

use Framework\App;
use Framework\Renderer\PHPRenderer;
use Framework\Renderer\TwigRenderer;
use GuzzleHttp\Psr7\ServerRequest;

require '../vendor/autoload.php';

$renderer = new TwigRenderer(dirname(__DIR__) . '/src/views');

$app = new App([
    \App\Blog\BlogModule::class
], [
    'renderer' => $renderer
]);

$response = $app->run(ServerRequest::fromGlobals());

\Http\Response\send($response);