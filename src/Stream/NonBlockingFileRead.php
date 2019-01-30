<?php

/**
 * Cooperative multitasking with event loop and pseudo non-blocking read
 */

/**
 * Event Loop
 */
class Loop
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var callable[]
     */
    private $ticks = [];

    /**
     * @var resource[]
     */
    private $readStreams = [];

    /**
     * @var callable[]
     */
    private $readCallbacks = [];

    public function __construct()
    {
        $this->context = new Context($this);
    }

    /**
     * @param callable $tick
     */
    public function addTick(callable $tick)
    {
        $this->ticks[] = Closure::bind($tick, $this->context);
    }

    /**
     * @param $readStream
     * @param callable $callback
     */
    public function addReadStream($readStream, callable $callback)
    {
        $readStreamId = (int)$readStream;

        $this->readStreams[$readStreamId] = $readStream;
        $this->readCallbacks[$readStreamId] = $callback;
    }

    /**
     * @param $readStream
     */
    public function removeReadStream($readStream)
    {
        $readStreamId = (int)$readStream;

        unset($this->readStreams[$readStreamId]);
        unset($this->readCallbacks[$readStreamId]);
    }

    /**
     * Loop iterator
     */
    public function run()
    {
        while (true) {
            // handle ticks
            foreach ($this->ticks as $tick) {
                call_user_func($tick);
            }
            $this->ticks = [];

            // handle streams
            $readStreams = $this->readStreams;
            $writeStreams = null;
            $exceptStreams = null;

            $modifiedStreamsCount = stream_select(
                $readStreams,
                $writeStreams,
                $exceptStreams,
                0
            );

            // add new client connection to pool if exists
            if ($modifiedStreamsCount === false) {
                echo "File read interrupted\n";
            } else if ($modifiedStreamsCount === 0) {
                echo "File not ready for reading\n";
            } else {
                echo sprintf("Selected %d files, ready to read\n", $modifiedStreamsCount);

                foreach ($readStreams as $readStream) {
                    $this->readCallbacks[(int)$readStream]($readStream);
                }
            }

            if (empty($this->ticks) && empty($this->readStreams)) {
                echo "No ticks and streams to handle. Exit event loop\n";
                break;
            }

            sleep(1);
        }
    }
}

/**
 * Context used to incapsulate loop from tick callbacks (user code)
 */
class Context
{
    /**
     * @var Loop
     */
    private $loop;

    /**
     * Context constructor.
     * @param Loop $loop
     */
    public function __construct(Loop $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function import(string $className)
    {
        return new $className($this->loop);
    }
}

/**
 * Non blocking file reader
 */
class FileReader
{
    /**
     * @var Loop
     */
    private $loop;

    /**
     * @var int
     */
    private $bufferSize = 4000;

    /**
     * @param Loop $loop
     */
    public function __construct(Loop $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param string $filename
     * @param callable $callback
     *
     * @throws \Exception
     */
    public function read(string $filename, callable $callback)
    {
        $file = fopen($filename, 'r');

        // file read is non blocking
        if (stream_set_blocking($file, 0) === false) {
            throw new \Exception('Error setting non blocking mode for stream');
        }

        // make read operations are unbuffered
        if (stream_set_read_buffer($file, 0) !== 0) {
            throw new \Exception('Error disabling read buffer');
        }

        $data = null;

        $this->loop->addReadStream(
            $file,
            function(&$file) use(&$data, $callback) {
                $dataChunk = stream_get_contents($file, $this->bufferSize);
                echo md5($dataChunk) . PHP_EOL;

                if ($dataChunk === '') {
                    $this->loop->removeReadStream($file);
                    $callback($data);
                } else {
                    $data .= $dataChunk;
                }
            }
        );
    }
}

$loop = new Loop();

/**
 * Entrypoint
 */
$loop->addTick(function() {
    echo 'Main loop start' . PHP_EOL;

    /** @var FileReader $fileReader */
    $fileReader = $this->import(FileReader::class);

    $fileReader->read(__FILE__, function (string $data) {
        echo sprintf("1) File size is: %d bytes\n", strlen($data));
    });

    $fileReader->read(__FILE__, function (string $data) {
        echo sprintf("2) File size is: %d bytes\n", strlen($data));
    });

    echo 'Main loop end' . PHP_EOL;
});

$loop->run();

/*
This will output:

Main loop start
Main loop end
Selected 2 files, ready to read
3db1fa34b5917b9820f5f275968effee
3db1fa34b5917b9820f5f275968effee
Selected 2 files, ready to read
479766c5eca1cc87f5ea50d5e63a47ed
479766c5eca1cc87f5ea50d5e63a47ed
Selected 2 files, ready to read
d41d8cd98f00b204e9800998ecf8427e
1) File size is: 4860 bytes
d41d8cd98f00b204e9800998ecf8427e
2) File size is: 4860 bytes
No ticks and streams to handle. Exit event loop

*/