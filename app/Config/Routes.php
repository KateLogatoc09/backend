<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/getData', 'MainController::getData');
$routes->get('/ListAll', 'Listing::ListAll');
$routes->get('/Ratings', 'Listing::Ratings');
$routes->match(['get', 'post'],'/Register', 'Account::Register_Tourist');
$routes->match(['get', 'post'],'/Login', 'Account::Login_Tourist');
$routes->match(['get', 'post'],'/Logout', 'Account::Logout_Tourist');
$routes->match(['get', 'post'],'/Tourist_Info', 'Account::Tourist_Info');
$routes->match(['get', 'post'],'/Tourist_Info_Edit', 'Account::Tourist_Info_Save');
$routes->match(['get', 'post'],'/Booking_Info_Past', 'Account::Past_Booking');

$routes->post('/save', 'MainController::save');
$routes->post('/del', 'MainController::del');