<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA LOGIN
$obRouter->get('/admin-panel/login', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function ($request) {
        return new Response(200, Admin\Login::getLogin($request));
    }
]);

//ROTA LOGIN (POST)
$obRouter->post('/admin-panel/login', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function ($request) {

        return new Response(200, Admin\Login::setLogin($request));
    }
]);

//ROTA LOGOUT (GET)
$obRouter->get('/admin-panel/logout', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {

        return new Response(200, Admin\Login::setLogout($request));
    }
]);
