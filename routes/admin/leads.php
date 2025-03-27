<?php

use App\Controller\Admin;
use App\Http\Response;

// ROTA DE LISTAGEM DE DEPOIMENTOS
$obRouter->get('/admin-panel/leads', [
    'middlewares' => [
        'required-admin-login',
    ],
    function ($request) {
        return new Response(200, Admin\Lead::getLeads($request));
    },
]);
