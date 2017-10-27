<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 22/09/17
 * Time: 13:29
 */

use App\Admin\AdminModule;
use App\Admin\AdminTwigExtension;
use App\Admin\Controllers\DashboardController;
use function DI\add;
use function DI\get;
use function DI\object;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => object()->constructor(get('admin.widgets')),
    AdminModule::class => object()->constructorParameter('prefix', get('admin.prefix')),
    DashboardController::class => object()->constructorParameter('widgets', get('admin.widgets'))
];