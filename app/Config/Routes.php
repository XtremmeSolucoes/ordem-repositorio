<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');


$routes->get('login', 'Login::novo');
$routes->get('logout', 'Login::logout');


$routes->get('esqueci', 'Password::esqueci');

/**
 * @todo criar a rota para ordens/minhas-ordens que é enviado no e-mail para o cliente
 */

// Agrupando as rotas do controller ContasPagar
$routes->group('contas', function($routes)
{
    $routes->add('/', 'ContasPagar::index');
    $routes->add('recuperacontas', 'ContasPagar::recuperaContas');

    $routes->add('buscaFornecedores', 'ContasPagar::buscaFornecedores');

    $routes->add('exibir/(:segment)', 'ContasPagar::exibir/$1');
    $routes->add('editar/(:segment)', 'ContasPagar::editar/$1');
    $routes->add('criar/', 'ContasPagar::criar');

    //Método post para atualização dos dados
    $routes->post('cadastrar', 'ContasPagar::cadastrar');
    $routes->post('atualizar', 'ContasPagar::atualizar');

    //Método post e GET para EXCLUSÃO dos dados
    $routes->match(['get', 'post'], 'excluir/(:segment)', 'ContasPagar::excluir/$1');

});

// Agrupando as rotas do controller Formas de Pagamentos
$routes->group('formas', function($routes)
{
    $routes->add('/', 'FormasPagamentos::index');
    $routes->add('recuperaformas', 'FormasPagamentos::recuperaFormas');

    $routes->add('exibir/(:segment)', 'FormasPagamentos::exibir/$1');
    $routes->add('editar/(:segment)', 'FormasPagamentos::editar/$1');
    $routes->add('criar/', 'FormasPagamentos::criar');

    //Método post para atualização dos dados
    $routes->post('cadastrar', 'FormasPagamentos::cadastrar');
    $routes->post('atualizar', 'FormasPagamentos::atualizar');

    //Método post e GET para EXCLUSÃO dos dados
    $routes->match(['get', 'post'], 'excluir/(:segment)', 'FormasPagamentos::excluir/$1');
});    

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
