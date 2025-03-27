<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/admin/paginas', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Pagina::getPaginas($request));
    }
]);



//ROTA DE EDIÇÃO DE UM DEPOIMENTOS
$obRouter->get('/admin/pagina-{nomeurl}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $nomeurl) {
        return new Response(200, Admin\Pagina::getDynEditPagina($request, $nomeurl));
    }
]);
//ROTA DE EDIÇÃO DE UM DEPOIMENTOS
$obRouter->post('/admin/pagina-{nomeurl}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $nomeurl) {
        return new Response(200, Admin\Pagina::setDynEditPagina($request, $nomeurl));
    }
]);



//ROTA DE EDIÇÃO DE UM DEPOIMENTOS (POST)
// $obRouter->post('/admin/paginas/{id}/edit', [
//     'middlewares' => [
//         'required-admin-login'
//     ],
//     function ($request, $id) {
//         return new Response(200, Admin\Pagina::setEditPagina($request, $id));
//     }
// ]);

// //ROTA DE EXCLUSÃO DE UM DEPOIMENTOS
// $obRouter->get('/admin/paginas/{id}/delete', [
//     'middlewares' => [
//         'required-admin-login'
//     ],
//     function ($request, $id) {
//         return new Response(200, Admin\Pagina::getDeletePagina($request, $id));
//     }
// ]);

// //ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
// $obRouter->post('/admin/paginas/{id}/delete', [
//     'middlewares' => [
//         'required-admin-login'
//     ],
//     function ($request, $id) {
//         return new Response(200, Admin\Pagina::setDeletePagina($request, $id));
//     }
// ]);

