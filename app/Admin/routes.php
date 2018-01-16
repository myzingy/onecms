<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    //$router->get('/', 'HomeController@index');
    $router->get('/',function (){
        return Redirect::to("/admin/question");
    });
    $router->resource('lecturer/apply', ExpertApplicationController::class);
    $router->resource('lecturer/users', ExpertController::class);
    $router->resource('lecturer/publicity', ExpertRecomController::class);

    $router->resource('/daily', DailyController::class);
    $router->resource('/paylog', PaylogController::class);
    $router->resource('/question', QuestionController::class);

    $router->any('/statistics/platform', 'StatisticsController@platform');
    $router->any('/statistics/lecturer', 'StatisticsController@lecturer');

    $router->any('/upload', 'UploadController@index');
    //$router->resource('/statistics', StatisticsController::class);

    $router->resource('/livebc_expert', LivebcExpertController::class);
    $router->resource('/livebc', LivebcController::class);
});
