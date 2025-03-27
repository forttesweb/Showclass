<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE USUARIOS
$obRouter->get('/admin-panel/lojas', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Loja::getLojas($request));
    }
]);

//ROTA DE CADASTRO DE NOVO USUARIO
$obRouter->get('/admin-panel/lojas/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Loja::getNewLoja($request));
    }
]);

//ROTA DE CADASTRO DE NOVO USUARIO (POST)
$obRouter->post('/admin-panel/lojas/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Loja::setNewLoja($request));
    }
]);

//ROTA DE EDIÇÃO DE UM USUARIO
$obRouter->get('/admin-panel/lojas/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Loja::getEditLoja($request, $id));
    }
]);

//ROTA DE EDIÇÃO DE UM USUARIO (POST)
$obRouter->post('/admin-panel/lojas/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Loja::setEditLoja($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM USUARIO
$obRouter->get('/admin-panel/lojas/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Loja::getDeleteLoja($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/lojas/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Loja::setDeleteLoja($request, $id));
    }
]);

