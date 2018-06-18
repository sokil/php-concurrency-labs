# PHP Concurrency

* [Definitions](#definitions)
* [Asynchronous code execution](#asynchronous-code-execution)
    * [Event loop extensions](#event-loop-extensions)
    * [Event loop frameworks](#event-loop-frameworks)
        * [ReactPHP](#reactphp)
        * [AMP](https://github.com/amphp)
        * [icicleio](https://github.com/icicleio)
        * [Kraken](http://kraken-php.com)
* [Multithread code execution](#multithread-code-execution)
  * [Thread extensions](#thread-extensions)
* [Coroutines](#coroutines)
* Inter-process communication
   * [PCNTL Signals](http://php.net/manual/en/book.pcntl.php)
   * [Socket](http://php.net/manual/en/book.sockets.php)
   * Shared memory
      * [Shmop](http://php.net/manual/en/book.shmop.php)
      * [Semaphore](http://php.net/manual/en/book.sem.php) - Semaphore, Shared Memory and IPC
      * [Sync](http://php.net/manual/en/book.sync.php) - Semaphore, Mutex, Shared Memory and IPC
   * Mutex, Semaphore
      * [Semaphore](http://php.net/manual/en/book.sem.php) - Semaphore, Shared Memory and IPC
      * [Sync](http://php.net/manual/en/book.sync.php) - Semaphore, Mutex, Shared Memory and IPC
   * [Named pipe](http://php.net/manual/ru/function.posix-mkfifo.php)
* Tools
    * [Process Control Extensions. PHP Manual](http://php.net/manual/en/refs.fileprocess.process.php)
        * [External program execution](http://php.net/manual/en/book.exec.php)
        * [PCNTL](http://php.net/manual/en/book.pcntl.php) - signals, forking of process
        * [POSIX](http://php.net/manual/en/book.posix.php) - process ids
     * [Stream](http://php.net/manual/ru/book.stream.php)
     * [PHP sources](https://github.com/php)
     * [Repositories related to the PHP Language](https://github.com/phplang)
* RFC
    * Fiber
      * [RFC](https://wiki.php.net/rfc/fiber)
      * [Source](http://fiberphp.org/)

## Definitions

There are two types of multitasking:

* [Co-operative, non-preemptive](https://en.wikipedia.org/wiki/Cooperative_multitasking):  is a style of computer multitasking when process voluntarily yield control and all programs must cooperate for the entire scheduling scheme to work.

* [Preemptive](https://en.wikipedia.org/wiki/Preemption_(computing)#PREEMPTIVE): involves the use of an interrupt mechanism which suspends the currently executing process and invokes a scheduler to determine which process should execute next. Therefore, all processes will get some amount of CPU time at any given time.

## Asynchronous code execution

### Event loop extensions

| Name | Extension | Source | Version | PHP version |
| -----|-----------|--------|---------|-------------|
| ext-libevent |[PHP Manual](http://php.net/manual/ru/book.libevent.php) | [PECL](https://pecl.php.net/package/libevent), [git.php.net](http://git.php.net/?p=pecl/event/libevent.git) | 0.1.0 | >= 5.3.0, < 7.0.0 |
| ext-event |[PHP Manual](http://php.net/manual/en/book.event.php) | [PECL](https://pecl.php.net/package/event), [Bitbucket](https://bitbucket.org/osmanov/pecl-event/src) | 2.3.0 | >= 5.4, >= 7.0 |
| ext-libev |[GitHub manual](https://github.com/m4rw3r/php-libev/) | [GitHub](https://github.com/m4rw3r/php-libev) | | < 7.0.0 |
| ext-ev |[PHP Manual](http://php.net/manual/en/intro.ev.php) | [PECL](https://pecl.php.net/package/ev), [BitBucket](https://bitbucket.org/osmanov/pecl-ev/src) | | >= 5.4, > 7.0 |
| eio |[PHP Manual](http://php.net/manual/en/intro.eio.php)|[PECL](http://pecl.php.net/package/eio), [GitHub](https://github.com/rosmanov/pecl-eio)|||
| swoole |[GitHub manual](https://github.com/swoole/swoole-src)|[GitHub](https://github.com/swoole/swoole-src)|||
| libuv |[GitHub](https://github.com/bwoebi/php-uv)|[PECL](https://pecl.php.net/package/uv), [GitHub](https://github.com/bwoebi/php-uv)|||

Links:
* [Benchmarking libevent against libev](http://libev.schmorp.de/bench.html) - libev faster

#### ext-libevent

Has build-in OpenSSL library, non-blocking IO, http, dns.

```
pecl install libevent-0.1.0
```

#### ext-event

Event is a PECL extension providing interface to libevent C library.

The libevent API provides a mechanism to execute a callback function when a specific event occurs on a file descriptor or after a timeout has been reached. Furthermore, libevent also support callbacks due to signals or regular timeouts.

Dockerfile: https://github.com/sokil/php-concurrency-labs/blob/master/docker/Dockerfile.ext-event

#### ext-libev

Library tries to improve `libevent`. But this is only event library, instead of libevent giving non-blocking IO, http, etc.

Install `libev` library:

```
sudo apt-get install libev-dev
```

Install php extension `ext-libev`. Clone https://github.com/m4rw3r/php-libev and build extension:

```
phpize
./configure --with-libev
make
make install
```

#### libuv

* [Official libuv site](http://libuv.org)
* [Libuv source](https://github.com/libuv/libuv)

### Event loop frameworks

#### ReactPHP

Source: https://reactphp.org

Examples: https://github.com/sokil/php-concurrency-labs/tree/master/examples/ReactPHP

```
cd src/ReactPHP
Docker build -t php-event .
docker run --rm -v `pwd`:/src php-event php /src/Timer.php
```

## Multithread code execution

### Thread extensions

|Name|Source|Manual|
|----|------|------|
|Pthreads|[GitHub](https://github.com/krakjoe/pthreads),[PECL](http://pecl.php.net/package/pthreads)|[PHP Manual](http://php.net/manual/ru/book.pthreads.php)|
|Pht|[GitHub](https://github.com/tpunt/pht)|[PHP Manual](http://php.net/manual/en/book.pht.php)|

#### Pthreads
      
Dockerfile: https://github.com/sokil/php-concurrency-labs/blob/master/docker/Dockerfile.ext-phtreads

## Coroutines

Coroutines are computer-program components that generalize subroutines for non-preemptive multitasking, by allowing multiple entry points for suspending and resuming execution at certain locations. Coroutines are well-suited for implementing familiar program components such as cooperative tasks, exceptions, event loops, iterators, infinite lists and pipes.

### Articles

* [Wikipedia](https://en.wikipedia.org/wiki/Coroutine)
* [Cooperative multitasking using coroutines](https://nikic.github.io/2012/12/22/Cooperative-multitasking-using-coroutines-in-PHP.html)
* [Co-operative PHP Multitasking](https://medium.com/async-php/co-operative-php-multitasking-ce4ef52858a0)
