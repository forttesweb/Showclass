<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA CADASTRO
$obRouter->get('/account/cadastro', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        return new Response(200, User\User::getNewUser($request));
    }
]);

//ROTA CADASTRO (POST)
$obRouter->post('/account/cadastro', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        
        return new Response(200, User\User::setNewUser($request));
    }
]);

//ROTA CADASTRO
$obRouter->get('/account/confirmar', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        return new Response(200, User\User::getConfirmEmail($request));
    }
]);

//ROTA CONFIRMAR CADASTRO (POST)
$obRouter->post('/account/confirmar', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {

        return new Response(200, User\User::ConfirmEmailUser($request));
    }
]);
//ROTA CONFIRMADO
$obRouter->get('/account/confirmado', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        return new Response(200, User\User::getConfirmed($request));
    }
]);
