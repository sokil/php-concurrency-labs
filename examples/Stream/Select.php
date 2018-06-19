<?php

$connectionString = 'tcp://localhost:9999';

// create listening socket
$serverSocket = stream_socket_server($connectionString,$errno,$errstr);
if ($serverSocket === false) {
    echo sprintf('Error #%d: %s', $errno, $errstr);
    exit;
}

// server is non blocking
stream_set_blocking($serverSocket, 0);

// pool of client connections
$clientSockets = [];

// request loop
while (true) {
    // gather new client connection
    $serverSocketReadStreams = [$serverSocket];
    $serverSocketWriteStreams = [];
    $serverSocketExceptStreams = [];

    $modifiedServerSocketStreamNumber = stream_select(
        $serverSocketReadStreams,
        $serverSocketWriteStreams,
        $serverSocketExceptStreams,
        0
    );

    if ($modifiedServerSocketStreamNumber === false) {
        echo "Server connection interrupted\n";
    } else if ($modifiedServerSocketStreamNumber === 0) {
        echo "No server streams modified\n";
    } else {
        echo sprintf("Selected %d server streams\n", $modifiedServerSocketStreamNumber);

        $serverSocket = $serverSocketReadStreams[0];
        $clientSocket = stream_socket_accept($serverSocket, 0);

        $clientSockets[] = $clientSocket;
    }

    // handle client connections
    if (count($clientSockets) > 0) {

        echo sprintf("Active client connections: %d\n", count($clientSockets));

        foreach ($clientSockets as $clientSocket) {
            fwrite($clientSocket, date('Y-m-d H:i:s.u') . PHP_EOL);
        }

        $clientSocketReadStreams = $clientSockets;
        $clientSocketWriteStreams = [];
        $clientSocketExceptStreams = [];

        $modifiedClientSocketStreamNumber = stream_select(
            $clientSocketReadStreams,
            $clientSocketWriteStreams,
            $clientSocketExceptStreams,
            0
        );

        if ($modifiedClientSocketStreamNumber === false) {
            echo "Client connection interrupted\n";
        } else if ($modifiedClientSocketStreamNumber === 0) {
            echo "No client streams modified\n";
        } else {
            echo sprintf("\033[0;33mSelected %d client streams\033[0m\n", $modifiedClientSocketStreamNumber);

            if (!empty ($clientSocketReadStreams)) {
                foreach ($clientSocketReadStreams as $clientSocketReadStream) {
                    echo sprintf(
                        "Read from client: \033[0;32m%s\033[0m",
                        fread($clientSocketReadStream, 1024)
                    );
                }
            }
        }
    }

    sleep(1);
}
