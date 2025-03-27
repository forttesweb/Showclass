<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/admin-panel/categorias', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Categoria::getCategorias($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS
$obRouter->get('/admin-panel/categorias/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Categoria::getNewCategoria($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/categorias/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Categoria::setNewCategoria($request));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/categorias/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Categoria::getEditCategoria($request, $id));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/categorias/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Categoria::setEditCategoria($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/categorias/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Categoria::getDeleteCategoria($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/categorias/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Categoria::setDeleteCategoria($request, $id));
    }
]);

