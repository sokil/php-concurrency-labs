# PHP Concurrency

* [Definitions](#definitions)
* [Asynchronous code execution](#asynchronous-code-execution)
    * [Event loop extensions](#event-loop-extensions)
    * [Event loop frameworks](#event-loop-frameworks)
      * [ReactPHP](#reactphp)
      * [AMP](#amp)
      * [icicleio](#icicleio) - deprecated in favor of [Amp v2.0](https://amphp.org)
      * [Kraken](#kraken)
* [Multithread code execution](#multithread-code-execution)
  * [Thread extensions](#thread-extensions)
* [Coroutines](#coroutines)
* [Distributed locking](#distributed-locking)
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
   * [Expect](http://php.net/manual/en/book.expect.php) -  allows to interact with processes through PTY
* [Process management](#process-management)
* [Tools](#tools)
     * External program execution
       * [System program execution (PHP manual)](http://php.net/manual/en/book.exec.php)
       * [popen (PHP manual)](http://php.net/manual/ru/function.popen.php)
     * [POSIX](http://php.net/manual/en/book.posix.php) - process ids
     * [Stream](#stream)
     * [PHP sources](https://github.com/php)
     * [Repositories related to the PHP Language](https://github.com/phplang)
* RFC
    * Fiber
      * [RFC](https://wiki.php.net/rfc/fiber)
      * [Source](http://fiberphp.org/) 
* [Articles](#articles)

## Definitions

[Concurrency](https://en.wikipedia.org/wiki/Concurrency_(computer_science)): ability of different parts of a program to be executed out-of-order.

[Parallelism](https://en.wikipedia.org/wiki/Task_parallelism): Form of [parallel computing](https://en.wikipedia.org/wiki/Parallel_computing) in which execution of processes are carried out concurrently across multiple processors in parallel computing environments.

[Multitaskink](https://en.wikipedia.org/wiki/Computer_multitasking): is the concurrent execution of multiple tasks over a certain period of time. There are two types of multitasking:
* [Co-operative, non-preemptive](https://en.wikipedia.org/wiki/Cooperative_multitasking):  is a style of computer multitasking when process voluntarily yield control and all programs must cooperate for the entire scheduling scheme to work.
   * Asynchronous code execution
   * Coroutines
* [Preemptive](https://en.wikipedia.org/wiki/Preemption_(computing)#PREEMPTIVE): involves the use of an interrupt mechanism which suspends the currently executing process and invokes a scheduler to determine which process should execute next. Therefore, all processes will get some amount of CPU time at any given time.
   * Threads
   * Forks

## Asynchronous code execution

### Event loop extensions

| Name | Extension | Source | Version | PHP version |
| -----|-----------|--------|---------|-------------|
| ext-libevent |[PHP Manual](http://php.net/manual/en/book.libevent.php) | [PECL](https://pecl.php.net/package/libevent), [git.php.net](http://git.php.net/?p=pecl/event/libevent.git) | 0.1.0 | >= 5.3.0, < 7.0.0 |
| ext-event |[PHP Manual](http://php.net/manual/en/book.event.php) | [PECL](https://pecl.php.net/package/event), [Bitbucket](https://bitbucket.org/osmanov/pecl-event/src) | 2.3.0 | >= 5.4, >= 7.0 |
| ext-libev |[GitHub manual](https://github.com/m4rw3r/php-libev/) | [GitHub](https://github.com/m4rw3r/php-libev) | | < 7.0.0 |
| ext-ev |[PHP Manual](http://php.net/manual/en/book.ev.php) | [PECL](https://pecl.php.net/package/ev), [BitBucket](https://bitbucket.org/osmanov/pecl-ev/src) | | >= 5.4, > 7.0 |
| eio |[PHP Manual](http://php.net/manual/en/intro.eio.php)|[PECL](http://pecl.php.net/package/eio), [GitHub](https://github.com/rosmanov/pecl-eio)|||
| swoole |[GitHub manual](https://github.com/swoole/swoole-src)|[GitHub](https://github.com/swoole/swoole-src)|||
| libuv |[GitHub](https://github.com/bwoebi/php-uv)|[PECL](https://pecl.php.net/package/uv), [GitHub](https://github.com/bwoebi/php-uv)|||
| concurent-php/ext-async | [GitHub manual](https://github.com/concurrent-php/ext-async) | [GitHub](https://github.com/concurrent-php/ext-async) | - | nightly | 

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

* [ReactPHP](#reactphp)
* [AMP](#amp)
* [icicleio](#icicleio)
* [Kraken](#kraken)
        
#### ReactPHP

Source: https://reactphp.org

Examples: https://github.com/sokil/php-concurrency-labs/tree/master/examples/ReactPHP

```
cd src/ReactPHP
Docker build -t php-event .
docker run --rm -v `pwd`:/src php-event php /src/TimerExample.php
```

##### Articles

* [Super Speed Symfony - ReactPHP](https://gnugat.github.io/2016/04/13/super-speed-sf-react-php.html)

#### AMP

Site: https://amphp.org

Source: https://github.com/amphp

#### icicleio

Icicle is now deprecated in favor of [Amp v2.0](https://amphp.org)

Source: https://github.com/icicleio

#### Kraken

Source: http://kraken-php.com

## Multithread code execution

### Thread extensions

|Name|Source|Manual|
|----|------|------|
|Pthreads|[GitHub](https://github.com/krakjoe/pthreads),[PECL](http://pecl.php.net/package/pthreads)|[PHP Manual](http://php.net/manual/ru/book.pthreads.php)|
|Pht|[GitHub](https://github.com/tpunt/pht)|[PHP Manual](http://php.net/manual/en/book.pht.php)|

#### Pthreads
      
Dockerfile: https://github.com/sokil/php-concurrency-labs/blob/master/docker/Dockerfile.ext-phtreads

Examples: https://github.com/sokil/php-concurrency-labs/tree/master/examples/Pthreads

## Coroutines

Coroutines are computer-program components that generalize subroutines for non-preemptive multitasking, by allowing multiple entry points for suspending and resuming execution at certain locations. Coroutines are well-suited for implementing familiar program components such as cooperative tasks, exceptions, event loops, iterators, infinite lists and pipes.

### Articles

* [Wikipedia](https://en.wikipedia.org/wiki/Coroutine)
* [Cooperative multitasking using coroutines](https://nikic.github.io/2012/12/22/Cooperative-multitasking-using-coroutines-in-PHP.html)
* [Co-operative PHP Multitasking](https://medium.com/async-php/co-operative-php-multitasking-ce4ef52858a0)

## Distributed locking

* [Distributed lock manager (Wiki)](https://en.wikipedia.org/wiki/Distributed_lock_manager)
* [Distributed locks with Redis](https://redis.io/topics/distlock)
* Consul
  * [CLI](https://www.consul.io/docs/commands/lock.html)
  * API
    * [Session](https://www.consul.io/api/session.html)
    * [Semaphore](https://www.consul.io/docs/guides/semaphore.html)
    * [KV Storage, aquiring](https://www.consul.io/api/kv.html)

## Process management

### Articles

* [Bring High Performance Into Your PHP App (with ReactPHP)](http://marcjschmidt.de/blog/2014/02/08/php-high-performance.html)

### Tools

* PHP-PM
  * GitHub: https://github.com/php-pm/php-pm
* Spatie 
  * https://github.com/spatie/async

## Tools

### Stream

PHP Manual: http://php.net/manual/ru/book.stream.php

Examples: https://github.com/sokil/php-concurrency-labs/tree/master/examples/Stream 

## Articles

* [The Reactive Manifesto](https://www.reactivemanifesto.org/)
* [Process Control Extensions (PHP Manual)](http://php.net/manual/en/refs.fileprocess.process.php)
* [How to use the Linux AIO feature](https://github.com/littledan/linux-aio)
