<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 13/09/17
 * Time: 09:43
 */

use DI\ContainerBuilder;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;

require dirname(__DIR__) . '/vendor/autoload.php';

$modules = [
    \App\Blog\BlogModule::class
];

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

$container = $builder->build();

$app = new App($container, $modules);

if (php_sapi_name() !== "cli") {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
