<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'TotemController::index');
$routes->get('language', 'TotemController::language');
$routes->get('menu', 'TotemController::mainMenu');
$routes->get('museo', 'TotemController::museum');
$routes->get('museo/coleccion', 'TotemController::museumCollection');
$routes->get('museo/historia-comica', 'TotemController::museumComicHistory');
$routes->get('museo/el-museo', 'TotemController::museumInfo');
$routes->get('historia', 'TotemController::history');
$routes->get('teatro-escuela', 'TotemController::theaterSchool');
$routes->get('cartelera', 'TotemController::billboard');
$routes->get('cartelera/detalle', 'TotemController::billboardDetail');
$routes->get('visitas-guiadas', 'TotemController::guidedVisits');
$routes->get('amigos-de-teatromuseo', 'TotemController::friends');
