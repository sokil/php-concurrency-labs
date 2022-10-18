<?php

function createEchoCoroutine(callable $callback): \Generator
{
    echo "Before callback" . PHP_EOL;
    $callback(yield);
    echo "After callback" . PHP_EOL;
}
echo "Before coroutine create" . PHP_EOL;
$coroutine = createEchoCoroutine(
    function (string $response) {
        echo "Received response: " . $response . PHP_EOL;
    }
);
echo "After coroutine create" . PHP_EOL;
echo "Before send" . PHP_EOL;
$coroutine->send('hello');
echo "After send";

/**
Before coroutine create
After coroutine create
Before send
Before callback
Received response: hello
After callback
After send
 */
