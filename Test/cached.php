<?php

include_once __DIR__ . '/../autoload.php';

Framework\Test::add(
    function () {
        $cache = new Framework\Cache();
        return ($cache instanceof Framework\Cache);
    },
    "Cache factory class can be created",
    "Cache"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "memcached"
        ));
        $cache = $cache->initialize();
        return ($cache instanceof Framework\Cache\Driver\Memcached);
    },
    "Cache\Driver\Memcached class can initialize",
    "Cache\Driver\Memcached"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "memcached"
        ));
        $cache = $cache->initialize();
        $connect = $cache->connect();
        var_dump($connect);
        return ($connect instanceof Framework\Cache\Driver\Memcached);
    },
    "Cache\Driver\Memcached class can connect and return itself",
    "Cache\Driver\Memcached"
);

$result = Framework\Test::run();
var_dump($result);
// var_dump($result['exceptions']);