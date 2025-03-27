<?php


# Sistema de chat com websocket em PHP
# Feito com carinho por Eduardo Palandrani
// use \Ratchet\Http\HttpServer;
// use \Ratchet\Server\IoServer;
// use \Ratchet\WebSocket\WsServer;
// use Ratchet\Server\IoServer;
// use Ratchet\Http\HttpServer;
// use Ratchet\WebSocket\WsServer;
// use App\Services\Socket;
// use WebSockets\MySocket;

use WebSockets\Chat;


use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
// use MyApp\Chat;

require __DIR__ . '/../vendor/autoload.php';


$loop = React\EventLoop\Factory::create();
$webSock = new React\Socket\Server('0.0.0.0:8085', $loop);
$webSock = new React\Socket\SecureServer($webSock, $loop, [
'local_cert' => '/etc/letsencrypt/live/showclass.com.br/fullchain.pem', // path to your cert
'local_pk' => '/etc/letsencrypt/live/showclass.com.br/privkey.pem', // path to your server private key
'allow_self_signed' => TRUE, // Allow self signed certs (should be false in production)
'verify_peer' => FALSE
]);
$webServer = new Ratchet\Server\IoServer(
new Ratchet\Http\HttpServer(
new Ratchet\WebSocket\WsServer(
new Chat()
)
),
$webSock, $loop
);

$webServer->run();


// $loop = React\EventLoop\Factory::create();

// // Set up our WebSocket server for clients wanting real-time updates
// $webSock = new React\Socket\Server('0.0.0.0:8085', $loop);
// $webSock = new React\Socket\SecureServer($webSock, $loop, [
//     'local_cert' => '/etc/letsencrypt/live/showclass.com.br/fullchain.pem',
//     'local_pk' => '/etc/letsencrypt/live/showclass.com.br/privkey.pem',
//     'allow_self_signed' => FALSE,
//     'verify_peer' => FALSE
// ]);

// $webServer = new Ratchet\Server\IoServer(
//     new Ratchet\Http\HttpServer(
//         new Ratchet\WebSocket\WsServer(
//                 new Socket
//         )
//     ),
//     $webSock
// );

// $loop->run();




// class Chat implements \Ratchet\MessageComponentInterface {
//     function onOpen(\Ratchet\ConnectionInterface $conn) { echo "connected.\n"; }
//     function onClose(\Ratchet\ConnectionInterface $conn) {}
//     function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {}
//     function onMessage(\Ratchet\ConnectionInterface $from, $msg) {}
// }

// $loop = \React\EventLoop\Factory::create(); // create EventLoop best for given environment
// $socket = new \React\Socket\Server('0.0.0.0:8080', $loop); // make a new socket to listen to (don't forget to change 'address:port' string)
// $server = new \Ratchet\Server\IoServer(
//     /* same things that go into IoServer::factory */
//     new \Ratchet\Http\HttpServer(
//         new \Ratchet\WebSocket\WsServer(
//             new MySocket() // dummy chat to test things out
//         )
//     ), 
//     /* our socket and loop objects */
//     $socket, 
//     $loop
// );

// $loop->addPeriodicTimer(1, function (\React\EventLoop\Timer\Timer $timer) {
//     echo "echo from timer!\n";
// });

// $server->run();



$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Socket()
        )
    ),
    8085
);

// $server->run();
// use Ratchet\MessageComponentInterface;
// use Ratchet\ConnectionInterface;

// class Chat implements MessageComponentInterface
// {
//     protected $clients;
//     public function __construct()
//     {
//         $this->clients = new \SplObjectStorage;
//         $this->setLog("Servidor iniciado!");
//     }
//     protected function setLog($log){ # Salva um Log de tudo o que acontece e exibe no console
//         echo date("Y-m-d H:i:s")." ".$log."\n";
//         fwrite(fopen("logs.log", 'a'), date("Y-m-d H:i:s")." ".$log."\n");
//     }
//     public function onOpen(ConnectionInterface $conn)
//     {
//         $this->clients->attach($conn);
//         echo "Nova conexão! ({$conn->resourceId})\n";
//     }
//     public function onMessage(ConnectionInterface $from, $msg)
//     {
//         foreach ($this->clients as $client) {
//             if ($from != $client) {
//                 $client->send($msg);
//             }
//         }
//     }
//     public function onClose(ConnectionInterface $conn)
//     {
//         $this->clients->detach($conn);
//         echo "Conexão {$conn->resourceId} foi fechada\n";
//     }
//     public function onError(ConnectionInterface $conn, \Exception $e)
//     {
//         echo "Ocorreu um erro: {$e->getMessage()}\n";
//         $conn->close();
//     }
// }

// $server = IoServer::factory(
//     new HttpServer(
//         new WsServer(
//             new Chat()
//         )
//     ),
//     8080
// );
// $server->run();
