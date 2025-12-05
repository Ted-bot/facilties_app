<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/facility/test', App\Controllers\FacilityController::class . '@test');
$router->post('/tags/create', App\Controllers\TagController::class . '@create');
$router->post('/tags/relation/create', App\Controllers\TagController::class . '@setTagAndFacililityRelation');

$router->post('/facilities/location/create', App\Controllers\LocationController::class . '@create');

$router->post('/facilities/create', App\Controllers\FacilityController::class . '@create');
$router->get('/facilities', App\Controllers\FacilityController::class . '@getAllFacilities');
$router->get('/facilities/(\d+)', App\Controllers\FacilityController::class . '@getOneFacility');
$router->post('/facilities/update/(\d+)/(\d+)', App\Controllers\FacilityController::class . '@updateOneFacility');

$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/', App\Controllers\IndexController::class . '@test');


$router->run();