<?php

require __DIR__ . '/vendor/autoload.php';

define('SERVER_HOST', '127.0.0.1');
define('SERVER_PORT', '8080');

$loop = \React\EventLoop\Factory::create();

// Statistics
$loop->addPeriodicTimer(5, function () {
    $memory = memory_get_usage() / 1024;
    $formatted = number_format($memory, 3) . 'K';
    echo "Current memory usage: {$formatted}\n";
});

// start server
$serverSocket = stream_socket_server('tcp://' . SERVER_HOST . ':' . SERVER_PORT);
stream_set_blocking(
    $serverSocket,
    false // NON-BLOCKING
);

// accept request
$clientConnectionList = [];

$loop->addReadStream(
    $serverSocket,
    function ($serverSocket) use (
        $loop,
        &$clientConnectionList
    ) {
        // establish connection
        $clientConnection = stream_socket_accept($serverSocket);
        $clientConnectionList[] = $clientConnection;

        echo 'Connection established' . PHP_EOL;
        fwrite($clientConnection, "Connection accepted\n");

        // wait incoming message
        $loop->addReadStream(
            $clientConnection,
            function($clientConnection) use (
                $loop,
                &$clientConnectionList
            ) {
                // get request
                $requestTime = date('d.m.Y H:i:s', time());
                $requestString = stream_socket_recvfrom($clientConnection, 1024);
                echo "Request string: " . $requestString;

                // send to all
                foreach ($clientConnectionList as $clientConnectionListItemId => $clientConnectionListItem) {
                    // send to socket
                    $loop->addWriteStream(
                        $clientConnectionListItem,
                        function($clientConnectionListItem) use(
                            $loop,
                            $requestTime,
                            $requestString,
                            &$clientConnectionList,
                            $clientConnectionListItemId
                        ) {
                            // send message to socket
                            $bytesSent = @stream_socket_sendto(
                                $clientConnectionListItem,
                                sprintf('[%s] %s', $requestTime, $requestString)
                            );

                            if ($bytesSent <= 0) {
                                unset($clientConnectionList[$clientConnectionListItemId]);
                            }

                            // stop handling socket in loop
                            $loop->removeWriteStream($clientConnectionListItem);
                        }
                    );

                }
            }
        );
    }
);

// start loop
echo 'Server started at ' . SERVER_HOST . ':' . SERVER_PORT . PHP_EOL;
$loop->run();

