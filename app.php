<?php

# Sistema de chat com websocket em PHP
# Feito com carinho por Eduardo Palandrani

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use WebSockets\MySocket;

require 'vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new MySocket()
        )
    ),
    80
);

$server->run();
