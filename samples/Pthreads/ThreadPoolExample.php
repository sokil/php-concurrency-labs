<?php

class SummariserThread extends Thread
{

    /**
     * List of numbers to sum
     *
     * @var array
     */
    private $numbersToSum;

    /**
     * Sum result
     *
     * @var int
     */
    private $sum;

    /**
     * @param int[] ...$numbersToSum
     */
    public function __construct(int ...$numbersToSum)
    {
        $this->numbersToSum = $numbersToSum;
    }

    /**
     * Execute thread
     */
    public function run()
    {
        sleep(10);
        $this->sum = array_sum((array)$this->numbersToSum);
    }

    /**
     * Get sum result
     *
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @return array
     */
    public function getNumbersToSum(): array
    {
        return (array)$this->numbersToSum;
    }
};

$pool = new Pool(4, Worker::class);

$data = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
    [10, 11, 12],
    [13, 14, 15],
];

$t = microtime(true);

/** @var SummariserThread[] $tasks */
$tasks = [];
foreach ($data as $dataChunk) {
    $task = new SummariserThread(...$dataChunk);
    $pool->submit($task);

    $tasks[] = $task;
}

while ($pool->collect() > 0) {
    echo 'Waiting...' . PHP_EOL;
}


// shutdown will wait for current queue to be completed
$pool->shutdown();

// get result
foreach ($tasks as $task) {
    echo sprintf(
        "Get result: %s, %s\n",
        json_encode($task->getNumbersToSum()),
        $task->getSum()
    );
}

echo 'Total executed time: ' . (microtime(true) - $t) . PHP_EOL;

