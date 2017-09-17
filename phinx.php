<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 17/09/17
 * Time: 11:37
 */

require 'public/index.php';

$migrations = [];

foreach ($modules as $module) {
    if ($module::MIGRATIONS) {
        $migrations[] = $module::MIGRATIONS;
    }
}

return [
    'paths' => [
        'migrations' => $migrations,
        'seeds' => __DIR__ .'/db'
    ],
    'environments' => [
        'default_database' => 'development',
        'development' => [
            'adapter' => $app->getContainer()->get('database.adapter'),
            'host' => $app->getContainer()->get('database.host'),
            'name' => $app->getContainer()->get('database.name'),
            'user' => $app->getContainer()->get('database.username'),
            'pass' => $app->getContainer()->get('database.password')
        ]
    ]
];
