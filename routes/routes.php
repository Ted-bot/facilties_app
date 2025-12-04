<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/facility/test', App\Controllers\FacilityController::class . '@test');
$router->post('/facility/create', App\Controllers\FacilityController::class . '@create');
$router->post('/facility/location/create', App\Controllers\LocationController::class . '@create');
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/', App\Controllers\IndexController::class . '@test');


$router->run();