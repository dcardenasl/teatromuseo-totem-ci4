<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'MainController::index');
$routes->get('language', 'MainController::language');
$routes->get('menu', 'MainController::mainMenu');

// Museo - Menu
$routes->get('museo', 'MuseumController::museum');

// Museo - Coleccion
$routes->get('museo/coleccion', 'CollectionController::collectionMain');
$routes->addRedirect('museo/coleccion/titeres', 'museo/coleccion/titeres/exhibicion', 301);
$routes->get('museo/coleccion/titeres/tecnicas', 'CollectionController::collectionTechniques');
$routes->get('museo/coleccion/titeres/exhibicion', 'CollectionController::collectionPuppetsExhibit');
$routes->get('museo/coleccion/titeres/tecnicas/(:segment)', 'CollectionController::collectionTechnique/$1');
$routes->addRedirect('museo/coleccion/titeres/(:segment)', 'museo/coleccion/titeres/tecnicas/$1', 301);
$routes->addRedirect('museo/coleccion/mascaras', 'museo/coleccion/mascaras/exhibicion', 301);
$routes->get('museo/coleccion/mascaras/exhibicion', 'CollectionController::collectionMasksExhibit');
$routes->get('museo/coleccion/mascaras/tradiciones', 'CollectionController::collectionMasksTraditions');
$routes->get('museo/coleccion/mascaras/tradiciones/(:segment)', 'CollectionController::collectionMaskTradition/$1');
$routes->get('museo/coleccion/fichas/(:num)', 'CollectionController::collectionItem/$1');

// Museo - Historia
$routes->get('museo/historia', 'MuseumController::museumHistoryMain');
$routes->get('museo/historia/(:segment)', 'MuseumController::museumHistoryPost/$1');
// Legacy aliases kept for existing QR codes and deep links
$routes->get('museo/historia-comica', 'MuseumController::museumComicHistoryMain');
$routes->get('museo/historia-comica/(:segment)', 'MuseumController::museumHistoryPost/$1');

// Museo - El Museo
$routes->get('museo/el-museo', 'MuseumController::museumInfoMain');
$routes->get('museo/el-museo/edificio', 'MuseumController::museumBuilding');
$routes->get('museo/el-museo/institucion', 'MuseumController::museumInstitution');
$routes->get('museo/el-museo/actualidad', 'MuseumController::museumToday');

// Teatro Escuela
$routes->get('teatro-escuela', 'SchoolController::theaterSchool');

// Extension
$routes->get('extension', 'FriendsController::extensionContact');
$routes->addRedirect('visitas-guiadas', 'extension');

// Otros
$routes->get('cartelera', 'BillboardController::billboard');
$routes->get('cartelera/detalle/(:any)', 'BillboardController::billboardDetail/$1');
$routes->get('amigos-de-teatromuseo', 'FriendsController::friends');

$routes->set404Override('App\Controllers\MainController::notFound');

// Health check endpoint for monitoring
$routes->get('health', 'HealthController::index');
