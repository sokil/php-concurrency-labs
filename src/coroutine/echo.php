<?php

function createEchoCoroutine(): \Generator
{
    echo "Before first yield" . PHP_EOL;
    echo yield . PHP_EOL;
    echo "After first yield" . PHP_EOL;
    echo "Before second yield" . PHP_EOL;
    echo yield . PHP_EOL;
    echo "After second yield" . PHP_EOL;
}

echo "Before coroutine create" . PHP_EOL;
$coroutine = createEchoCoroutine();
echo "After coroutine create" . PHP_EOL;

echo "Before send hello" . PHP_EOL;
$coroutine->send('hello');
echo "After send hello" . PHP_EOL;

echo "Before send world" . PHP_EOL;
$coroutine->send('world');
echo "After send world";

/**
Before coroutine create
After coroutine create
Before send hello
Before first yield
hello
After first yield
Before second yield
After send hello
Before send world
world
After second yield
After send world
 */
