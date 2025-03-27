<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/admin-panel/testimonies', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Testimony::getTestimonies($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS
$obRouter->get('/admin-panel/testimonies/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Testimony::getNewTestimony($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/testimonies/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Testimony::setNewTestimony($request));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/testimonies/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Testimony::getEditTestimony($request, $id));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/testimonies/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Testimony::setEditTestimony($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/testimonies/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Testimony::getDeleteTestimony($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/testimonies/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Testimony::setDeleteTestimony($request, $id));
    }
]);

