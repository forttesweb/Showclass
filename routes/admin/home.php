<?php 

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA ADMIN
$obRouter->get('/admin-panel', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Home::getHome($request));
    }
]);