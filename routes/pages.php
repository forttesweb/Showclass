<?php

use App\Controller\Pages;
use App\Http\Response;

// ROTA HOME
$obRouter->get('/', [
    function ($request) {
        return new Response(200, Pages\Home::getHome($request));
    },
]);

// ROTA SOBRE
$obRouter->get('/sobre', [
    function () {
        return new Response(200, Pages\About::getAbout());
    },
]);

// ROTA SOBRE
$obRouter->get('/perguntas-frequentes', [
    function () {
        return new Response(200, Pages\Pergunta::getPergunta());
    },
]);

// ROTA SOBRE
$obRouter->get('/dicas-de-seguranca', [
    function () {
        return new Response(200, Pages\Dicas::getDicas());
    },
]);

// ROTA SOBRE
$obRouter->get('/politica-de-privacidade', [
    function () {
        return new Response(200, Pages\Politica::getPolitica());
    },
]);

// ROTA SOBRE
$obRouter->get('/ajuda', [
    function () {
        return new Response(200, Pages\Ajuda::getAjuda());
    },
]);

// ROTA PLANOS
$obRouter->get('/planos', [
    function () {
        return new Response(200, Pages\Plans::getPlans());
    },
]);

// ROTA PLANOS
$obRouter->get('/chats', [
    'middlewares' => [
        'required-user-login',
    ],
    function ($request) {
        return new Response(200, Pages\Chat::getChat($request));
    },
]);

// ROTA PLANOS
$obRouter->get('/notificacoes', [
    'middlewares' => [
        'required-user-login',
    ],
    function ($request) {
        return new Response(200, Pages\Notificacao::getNotificacoes($request));
    },
]);

// ROTA PLANOS
$obRouter->get('/fetch_chat', [
    function ($request) {
        return new Response(200, Pages\Chat::change_chat_status($request));
    },
]);
// ROTA PLANOS
$obRouter->post('/fetch_chat', [
    function ($request) {
        return new Response(200, Pages\Chat::change_chat_status($request));
    },
]);

// ROTA DE ASSINATURA DE PLANO
$obRouter->get('/assinar/{id_plano}', [
    'middlewares' => [
        'required-user-login',
    ],
    function ($request, $id_plano) {
        return new Response(200, Pages\Plans::getPlan($request, $id_plano));
    },
]);

// //ROTA DE ASSINATURA DE PLANO
// $obRouter->get('/assinar/{id_plano}', [
//     function ($request, $id_plano) {
//         return new Response(200, Pages\Plans::getPlan($request, $id_plano));
//     }
// ]);

$obRouter->post('/assinatura/cancelarRecorrencia', [
    function ($request, $id_plano) {
        return new Response(200, Pages\Assinatura::cancelarRecorrencia($request));
    },
]);

$obRouter->post('/assinatura/pagamento', [
    function ($request) {
        return new Response(200, Pages\Assinatura::pagamento($request), 'application/json');
    },
]);
$obRouter->post('/assinatura/webhook', [
    function ($request) {
        return new Response(200, Pages\Assinatura::webhook($request), 'application/json');
    },
]);
$obRouter->get('/assinatura/verify', [
    function ($request) {
        return new Response(200, Pages\Assinatura::verify(), 'application/json');
    },
]);

$obRouter->post('/parcelas', [
    function ($request) {
        return new Response(200, Pages\Assinatura::parcelas($request), 'application/json');
    },
]);

// ROTA ANUNCIO
$obRouter->get('/anunciar', [
    'middlewares' => [
        'required-user-login',
    ],
    function ($request) {
        return new Response(200, Pages\Announce::getAnnounce($request));
    },
]);
// ROTA PLANOS
$obRouter->post('/anunciar2', [
    'middlewares' => [
        'required-user-login',
    ],
    function ($request) {
        return new Response(200, Pages\Announce::SetAnnounce($request));
    },
]);

// ROTA UPLOAD DE FOTOS
$obRouter->post('/uploadfotos', [
    'middlewares' => [
        'required-user-login',
    ],
    function ($request) {
        return new Response(200, Pages\Announce::UploadFotos($request));
    },
]);
// ROTA DA PÁGINA DO ANÚNCIO
$obRouter->get('/anuncio/{loja}/{anuncio}', [
    function ($request, $loja, $anuncio) {
        return new Response(200, Pages\Announce::getAds($request, $loja, $anuncio));
    },
]);
// ROTA DA PÁGINA DO ANÚNCIO
$obRouter->get('/anuncio/{loja}', [
    function ($request, $loja, $anuncio) {
        return new Response(200, Pages\Store::getStoreAds($request, $loja, $anuncio));
    },
]);

// ROTA DA LOJA DO ANUNCIANTE
$obRouter->get('/busca', [
    function ($request, $busca) {
        return new Response(200, Pages\Search::getSearch($request, $busca));
    },
]);
// ROTA DA LOJA DO ANUNCIANTE
$obRouter->get('/busca_items', [
    function ($request, $busca) {
        return new Response(200, Pages\Search::getSearchItems($request, $busca));
    },
]);
$obRouter->post('/busca', [
    function ($request, $busca) {
        return new Response(200, Pages\Search::getSearchAjax($request, $busca));
    },
]);

// ROTA DA LOJA DO ANUNCIANTE
$obRouter->get('/ver_stories', [
    function ($request) {
        return new Response(200, Pages\Home::getHomeStories($request), 'application/json');
    },
]);
// ROTA DA LOJA DO ANUNCIANTE
$obRouter->post('/ver_stories', [
    function ($request) {
        return new Response(200, Pages\Home::getHomeStories($request), 'application/json');
    },
]);

// ROTA DINÂMINA
// $obRouter->get('/pagina/{idPagina}/{acao}', [
//     function ($idPagina,$acao) {
//         return new Response(200, 'Página'.$idPagina.' - '.$acao);
//     }
// ]);

// ROTA AVALIACAO (INSERT)
$obRouter->post('/avaliar_loja', [
    function ($request) {
        return new Response(200, Pages\Store::insertReview($request));
    },
]);

// ROTA AVALIACAO (INSERT)
$obRouter->post('/seguir_loja', [
    function ($request) {
        return new Response(200, Pages\Store::insertSeguidor($request));
    },
]);
// ROTA AVALIACAO (INSERT)
$obRouter->post('/deixar_seguir', [
    function ($request) {
        return new Response(200, Pages\Store::RemoverSeguidor($request));
    },
]);

// ROTA DEPOIMENTOS (INSERT)
$obRouter->post('/depoimentos', [
    function ($request) {
        return new Response(200, Pages\Testimony::insertTestimony($request));
    },
]);

$obRouter->post('/contatoloja/{loja}', [
    function ($request, $loja) {
        return new Response(200, Pages\Announce::setContato($request, $loja));
    },
]);

//ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/pages/pagamentos', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request) {
        return new Response(200, Pages\Pagamento::getPagamentos($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS
$obRouter->get('/pages/pagamentos/new', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request) {
        return new Response(200, Pages\Pagamento::getNewPagamento($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS
$obRouter->post('/pages/pagamentos/cancelar_assinatura', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request) {
        return new Response(200, Pages\Pagamento::setCancelAssinatura($request));
    }
]);

//ROTA DE CADASTRO DE NOVO DEPOIMENTOS (POST)
$obRouter->post('/pages/pagamentos/new', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request) {
        return new Response(200, Pages\Pagamento::setNewPagamento($request));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS
$obRouter->get('/pages/pagamentos/{id}/edit', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request, $id) {
        return new Response(200, Pages\Pagamento::getEditPagamento($request, $id));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/pages/pagamentos/{id}/edit', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request, $id) {
        return new Response(200, Pages\Pagamento::setEditPagamento($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS
$obRouter->get('/pages/pagamentos/{id}/delete', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request, $id) {
        return new Response(200, Pages\Pagamento::getDeletePagamento($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/pages/pagamentos/{id}/delete', [
    'middlewares' => [
        'required-jwtauth-login'
    ],
    function ($request, $id) {
        return new Response(200, Pages\Pagamento::setDeletePagamento($request, $id));
    }
]);
//ROTA DE EXCLUSÃO DE UM DEPOIMENTOS (POST)
$obRouter->post('/pages/pagamentos/notification', [
    function ($request) {
        return new Response(200, Pages\Pagamento::getNotification($request));
    }
]);

