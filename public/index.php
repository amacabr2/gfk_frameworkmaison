<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 13/09/17
 * Time: 09:43
 */

use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;

require '../vendor/autoload.php';

$app = new App();

$response = $app->run(ServerRequest::fromGlobals());

\Http\Response\send($response);