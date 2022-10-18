<?php

$pid = pcntl_fork();
if ($pid == -1) {
    echo 'error' . PHP_EOL;
} else if ($pid) {
    echo 'parent, waiting for child' . PHP_EOL;
    pcntl_wait($status); // wait before all children exited
    echo 'all children exited' . PHP_EOL;
} else {
    sleep(5);
    echo 'child' . PHP_EOL;
}

