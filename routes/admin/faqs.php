<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/admin-panel/faqs', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Faq::getFaqs($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS
$obRouter->get('/admin-panel/faqs/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Faq::getNewFaq($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/faqs/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Faq::setNewFaq($request));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/faqs/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Faq::getEditFaq($request, $id));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/faqs/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Faq::setEditFaq($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS
$obRouter->get('/admin-panel/faqs/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Faq::getDeleteFaq($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/admin-panel/faqs/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Faq::setDeleteFaq($request, $id));
    }
]);

