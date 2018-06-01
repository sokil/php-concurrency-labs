# PHP Extension List

* [Process Control Extensions. PHP Manual](http://php.net/manual/en/refs.fileprocess.process.php)
* [Stream](http://php.net/manual/ru/book.stream.php)
* Threads
  * Pthreads
    * [Source](https://github.com/krakjoe/pthreads)
    * [PHP Manual](http://php.net/manual/ru/book.pthreads.php)
  * The Pht Threading Extension
    * [Source](https://github.com/tpunt/pht)
    * [PHP Manual](http://php.net/manual/en/book.pht.php)

## Event extensions

| Name | Extension | Source | Version | PHP version |
| -----|-----------|--------|---------|-------------|
| ext-libevent |[PHP Manual](http://php.net/manual/ru/book.libevent.php) | [PECL](https://pecl.php.net/package/libevent) | 0.1.0 | >= 5.3.0, < 7.0.0 |
| ext-event |[PHP Manual](http://php.net/manual/en/book.event.php) | [PECL](https://pecl.php.net/package/event) | 2.3.0 ||
| ext-libev |[PHP Manual](http://php.net/manual/en/intro.ev.php), [GitHub manual](https://github.com/m4rw3r/php-libev/) | [GitHub](https://github.com/m4rw3r/php-libev) | | < 7.0.0 |
| eio |[PHP Manual](http://php.net/manual/en/intro.eio.php)|[PECL](http://pecl.php.net/package/eio), [GitHub](https://github.com/rosmanov/pecl-eio)|||
| swoole |[GitHub manual](https://github.com/swoole/swoole-src)|[GitHub](https://github.com/swoole/swoole-src)|||

Links:
* [Benchmarking libevent against libev](http://libev.schmorp.de/bench.html) - libev faster

### ext-libevent

Has build-in OpenSSL library, non-blocking IO, http, dns.

```
pecl install libevent-0.1.0
```

### ext-event

```
pecl install event-0.1.0
```

### ext-libev

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
