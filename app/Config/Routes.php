<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Accounting');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

$routes->get('/', 'Accounting::index');
$routes->post('/add-transaction', 'Accounting::addTransaction');
$routes->match(['get', 'post'], '/edit-transaction/(:num)', 'Accounting::editTransaction/$1');
$routes->get('/delete-transaction/(:num)', 'Accounting::deleteTransaction/$1');
$routes->get('/generate-income-statement', 'Accounting::generateIncomeStatement');
$routes->get('/generate-balance-sheet', 'Accounting::generateBalanceSheet');
$routes->get('/export-excel', 'Accounting::exportExcel');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}