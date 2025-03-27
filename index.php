<?php

require __DIR__.'/includes/app.php';
// require __DIR__.'/includes/websocket.php';

use \App\Http\Router;

$obRouter = new Router(URL);

//INCLUI AS ROTAS DE PÁGINAS
include __DIR__ . '/routes/pages.php';

//INCLUI AS ROTAS DO PAINEL
include __DIR__ . '/routes/admin.php';

//INCLUI AS ROTAS DO PAINEL DE USUÁRIO
include __DIR__ . '/routes/account.php';

//INCLUI AS ROTAS DA API
include __DIR__ . '/routes/api.php';

//IMPRIME O RESPONSE DA PAGINA
$obRouter->run()
    ->sendResponse();
