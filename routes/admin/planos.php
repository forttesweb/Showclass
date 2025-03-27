<?php

use \App\Http\Response;
use \App\Controller\Admin;

// ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/admin-panel/planos', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Plano::getPlanos($request));
    }
]);

// ROTA DE CADASTRO DE NOVO DEPOIMENTOS
$obRouter->get('/admin-panel/planos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Plano::getNewPlano($request));
    }
]);

// ROTA DE CADASTRO DE NOVO DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/planos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Plano::setNewPlano($request));
    }
]);

// ROTA DE EDIÇÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/planos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Plano::getEditPlano($request, $id));
    }
]);

// ROTA DE EDIÇÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/planos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Plano::setEditPlano($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/planos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Plano::getDeletePlano($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/planos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Plano::setDeletePlano($request, $id));
    }
]);

// ROTA PARA VISUALIZAR OS RECURSOS DE UM PLANO
$obRouter->get('/admin-panel/planos/recursos/{id}', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Plano::getRecursosPlano($request, $id));
    }
]);

// ROTA PARA ATUALIZAR OS RECURSOS DE UM PLANO (POST)
$obRouter->post('/admin-panel/planos/recursos/{id}', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Plano::updateRecursosPlano($request, $id));
    }
]);
