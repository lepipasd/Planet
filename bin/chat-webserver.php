<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Portal\Chat;

    require dirname(__DIR__) . '/vendor/autoload.php';

    echo "Starting web server ... \n";
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        9999
    );
    echo PHP_VERSION;
    echo "\n";

    $server->run();
