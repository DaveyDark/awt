<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/create', 'Home::create');
$routes->get('/toggle/(:num)', 'Home::toggle/$1');
$routes->get('/delete/(:num)', 'Home::delete/$1');
