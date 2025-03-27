<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA LOGIN
$obRouter->get('/account/login', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        return new Response(200, User\Login::getLogin($request));
    }
]);

//ROTA LOGIN (POST)
$obRouter->post('/account/login', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {

        return new Response(200, User\Login::setLogin($request));
    }
]);

//ROTA LOGOUT (GET)
$obRouter->get('/account/logout', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {

        return new Response(200, User\Login::setLogout($request));
    }
]);


//ROTA LOGIN GMAIL (POST)
$obRouter->post('/account/login_google', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {

        return new Response(200, User\Login::setLoginGoogle($request));
    }
]);

//ROTA LOGOUT (GET)
$obRouter->get('/account/recuperar-senha', [
    function ($request) {

        return new Response(200, User\Login::getSenha($request));
    }
]);

//ROTA LOGOUT (GET)
$obRouter->post('/account/recuperar-senha', [
    function ($request) {

        return new Response(200, User\Login::setSenha($request), 'application/json');
    }
]);


//ROTA LOGOUT (GET)
$obRouter->get('/account/nova-senha', [
    function ($request) {

        return new Response(200, User\Login::getNovaSenha($request));
    }
]);

//ROTA LOGOUT (GET)
$obRouter->post('/account/nova-senha', [
    function ($request) {

        return new Response(200, User\Login::setNovaSenha($request));
    }
]);