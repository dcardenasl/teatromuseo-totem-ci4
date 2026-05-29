<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'TotemController::index');
$routes->get('language', 'TotemController::language');
$routes->get('menu', 'TotemController::mainMenu');

// Museo - Coleccion
$routes->get('museo/coleccion', 'TotemController::collectionMain');
$routes->get('museo/coleccion/titeres', 'TotemController::collectionTechniques');
$routes->get('museo/coleccion/titeres/(:segment)', 'TotemController::collectionTechnique/$1');
$routes->get('museo/coleccion/mascaras', 'TotemController::collectionMasks');
$routes->get('museo/coleccion/mascaras/(:segment)', 'TotemController::collectionMaskTradition/$1');
$routes->get('museo/coleccion/payasos', 'TotemController::collectionClowns');
$routes->get('museo/coleccion/fichas/(:num)', 'TotemController::collectionItem/$1');

// Museo - Historia Comica
$routes->get('museo/historia-comica', 'TotemController::museumComicHistoryMain');
$routes->get('museo/historia-comica/(:segment)', 'TotemController::museumHistoryPost/$1');

// Museo - El Museo
$routes->get('museo/el-museo', 'TotemController::museumInfoMain');
$routes->get('museo/el-museo/edificio', 'TotemController::museumBuilding');
$routes->get('museo/el-museo/institucion', 'TotemController::museumInstitution');
$routes->get('museo/el-museo/actualidad', 'TotemController::museumToday');

// Teatro Escuela
$routes->get('teatro-escuela', 'TotemController::theaterSchool');

// Extension
$routes->get('extension', 'TotemController::extensionContact');
$routes->addRedirect('visitas-guiadas', 'extension');

// Otros
$routes->get('cartelera', 'TotemController::billboard');
$routes->get('cartelera/detalle/(:any)', 'TotemController::billboardDetail/$1');
$routes->get('amigos-de-teatromuseo', 'TotemController::friends');
