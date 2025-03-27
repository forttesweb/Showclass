<?php

use App\Controller\User;
use App\Http\Response;

// ROTA ADMIN
$obRouter->get('/account', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        return new Response(200, User\Home::getHome($request));
    },
]);
// ROTA ADMIN
$obRouter->get('/account/delete', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        
        return new Response(200, User\Home::getDelete($request));
    },
]);
// ROTA ADMIN
$obRouter->post('/account/delete', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {

        return new Response(200, User\User::setDeleteUser($request));
    },
]);
// ROTA ADMIN
$obRouter->get('/account/deletar/{anuncio}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $anuncio) {
        return new Response(200, User\Anuncio::getDeleteAnuncio($request, $anuncio));
    },
]);
// ROTA ADMIN
$obRouter->post('/account/deletar/{anuncio}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        return new Response(200, User\Anuncio::setDeleteAnuncio($request));
    },
]);
// ROTA DE EDIÇÃO DE UM USUARIO (POST)
$obRouter->post('/account/{id_loja}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $id_loja) {
        return new Response(200, User\Home::setEditStore($request, $id_loja));
    },
]);
// ROTA DE EDIÇÃO DE UM USUARIO (POST)
$obRouter->post('/account', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        return new Response(200, User\Home::setEditUser($request));
    },
]);
// ROTA DE EDIÇÃO DE UM USUARIO (POST)
$obRouter->post('/inserir_storie/{id_loja}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $id_loja) {
        return new Response(200, User\Home::setStories($request, $id_loja));
    },
]);

// ROTA DE EDIÇÃO DE UM USUARIO (POST)
$obRouter->post('/marcar_vendido', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        return new Response(200, User\Home::marcarVendido($request));
    },
]);
// ROTA DE EDIÇÃO DE UM USUARIO (POST)
$obRouter->post('/editar_endereco/{id_user}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $id_user) {
        return new Response(200, User\Home::setEditEndereco($request, $id_user));
    },
]);
// ROTA ADMIN
$obRouter->get('/dados_anuncio/{id_anuncio}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $id_anuncio) {
        return new Response(200, User\Anuncio::getDadosAnuncio($request, $id_anuncio));
    },
]);
// ROTA ADMIN
$obRouter->post('/editar_anuncio', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        return new Response(200, User\Anuncio::editarAnuncio($request));
    },
]);
// ROTA ADMIN
$obRouter->post('/listar_imagens_anuncio/{id_anuncio}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $id_anuncio) {
        return new Response(200, User\Anuncio::getImagensAnuncio($request, $id_anuncio));
    },
]);
// ROTA ADMIN
$obRouter->post('/inserir_imagens_anuncio', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        return new Response(200, User\Anuncio::setImagensAnuncio($request));
    },
]);
// ROTA ADMIN
$obRouter->post('/excluir_imagem_anuncio', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        return new Response(200, User\Anuncio::deleteProdutosFoto($request));
    },
]);
$obRouter->get('/editar_anuncio/{id_anuncio}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $id_anuncio) {
        return new Response(200, User\Anuncio::getEditAnuncio($request, $id_anuncio));
    },
]);
$obRouter->post('/editar_anuncio/{id_anuncio}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request, $id_anuncio) {
        return new Response(200, User\Anuncio::setEditAnuncio($request, $id_anuncio));
    },
]);

$obRouter->post('/account/deletar/storie/{storie}', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
       
        return new Response(200, User\Home::setDeleteStorie($request));
    },
]);

$obRouter->get('/account/deletar/storie', [
    'middlewares' => [
        'required-user-login',
        'required-user-confirmation',
    ],
    function ($request) {
        echo'vem';exit();
        // return new Response(200, User\Home::getDeleteStorie($request, $id));
    },
]);

// ROTA ADMIN
// $obRouter->post('/anuncio/delete_foto', [
//     'middlewares' => [
//         'required-user-login',
//         'required-user-confirmation',
//     ],
//     function ($request) {
//         return new Response(200, User\Anuncio::deleteProdutosFoto($request));
//     },
// ]);
