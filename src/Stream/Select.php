<?php

$connectionString = 'tcp://localhost:9999';

$readStreams = [stream_socket_server($connectionString)];
$writeStreams = [];
$exceptStreams = [];

echo sprintf("Server %s started \n", $connectionString);

while (true) {
    $modifiedStreamNum = stream_select($readStreams, $writeStreams, $exceptStreams, 0);
    if ($modifiedStreamNum === false) {
        echo "Interrupted\n";
    } else if ($modifiedStreamNum === 0) {
        echo "No streams modified\n";
    } else {
        echo sprintf("Selected %d resources\n", $modifiedStreamNum);

        $stream = $readStreams[0];
        
    }

    sleep(1);
}
