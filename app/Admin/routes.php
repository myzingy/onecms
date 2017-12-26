<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('lecturer/apply', ExpertApplicationController::class);

    $router->any('/statistics/platform', 'StatisticsController@platform');
    $router->any('/statistics/lecturer', 'StatisticsController@lecturer');
    //$router->resource('/statistics', StatisticsController::class);
});
