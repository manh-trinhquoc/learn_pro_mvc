<?php

include_once __DIR__ . '/../autoload.php';

Framework\Test::add(
    function () {
        $cache = new Framework\Cache();
        return ($cache instanceof Framework\Cache);
    },
    "Cache instantiates in uninitialized state",
    "Cache"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "memcached"
        ));
        return true;
        // $cache = $cache->initialize();
        // return ($cache instanceof Framework\Cache\Driver\Memcached);
    },
    "Cache\Driver\Memcached initializes",
    "Cache\Driver\Memcached"
);

$result = Framework\Test::run();
var_dump($result);