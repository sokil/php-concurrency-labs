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
};

$summariserThread = new SummariserThread(1, 2, 3, 4, 5, 6, 7);

if (!$summariserThread->start()) {
    echo 'Thread not started';
}

if (!$summariserThread->join()) {
    echo 'Thread not joined';
}

echo $summariserThread->getSum() . PHP_EOL;