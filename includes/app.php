<?php

ini_set('display_errors', false); // Habilita a exibição de erros
require __DIR__.'/../vendor/autoload.php';

date_default_timezone_set('America/Sao_Paulo');

use App\Http\Middleware\Queue as MiddlewareQueue;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Database;
use WilliamCosta\DotEnv\Environment;

// CARREGA VARIVEIS DE AMBIENTE
Environment::load(__DIR__.'/../');

// DEFINE AS CONFIGURAÇÕES DE BANCO DE DADOS
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

// DEFINE  A CONSTANTE DE URL
define('URL', getenv('URL'));

define('PUBLICO', __DIR__.'/../publico/lojas/');
define('STORIES', __DIR__.'/../publico/stories/');
define('PUBLICOSITE', __DIR__.'/../publico/site/');

define('EMAIL_LOJA', getenv('EMAIL_LOJA'));

// DEFINE O VALOR PADRÃO DAS VARIVEIS
View::init([
    'URL' => URL,
    'PUBLICO' => PUBLICO,
    'EMAIL_LOJA' => EMAIL_LOJA,
    'PUBLICOSITE' => PUBLICOSITE,
]);

// DEFINE O MAPEAMENTO DE MIDDLEWARES
MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
    'required-user-logout' => \App\Http\Middleware\RequireUserLogout::class,
    'required-user-login' => \App\Http\Middleware\RequireUserLogin::class,
    'required-user-confirmation' => \App\Http\Middleware\RequireEmailConfirmation::class,
    'api' => \App\Http\Middleware\Api::class,
    'user-basic-auth' => \App\Http\Middleware\UserBasicAuth::class,
]);

// DEFINE O MAPEAMENTO DE MIDDLEWARES PADRÕES (EXECUTADOS EM TODAS AS ROTAS)
MiddlewareQueue::setDefault([
    'maintenance',
]);

$host = '192.185.208.37';
$port = 80;

$connection = fsockopen($host, $port, $errno, $errstr, 1);

// if ($connection) {
//     echo "A porta $port está aberta";
// }
if ($connection) {
} else {
    require __DIR__.'/../includes/websocket.php';
}
