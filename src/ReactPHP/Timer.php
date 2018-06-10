<?php

require __DIR__ . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

echo get_class($loop) . PHP_EOL;

$loop->futureTick(function() {
    echo 'TICK' . PHP_EOL;
});

$loop->addPeriodicTimer(1, function () {
    for ($i = 0; $i < 4; $i++) {
        echo 'Timer 1' . PHP_EOL;
        sleep(1); # blocks loop
    }
});

$loop->addPeriodicTimer(1, function () {
    for ($i = 0; $i < 4; $i++) {
        echo 'Timer 2' . PHP_EOL;
        sleep(1); # blocks loop
    }
});

$loop->addSignal(SIGINT, function() {
    die('SIGNAL HANDLED' . PHP_EOL);
});

$loop->run();