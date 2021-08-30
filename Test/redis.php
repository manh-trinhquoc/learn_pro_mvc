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
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        return ($cache instanceof Framework\Cache\Driver\Redis);
    },
    "Cache\Driver\Redis class can initialize",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache->prefix = 'manhtq';
        return ($cache->prefix == 'manhtq');
    },
    "Cache\Driver\Redis class can set prefix",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $connect = $cache->connect();
        return ($connect instanceof Framework\Cache\Driver\Redis);
    },
    "Cache\Driver\Redis class can connect and return itself",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache = $cache->connect();
        $cache = $cache->disconnect();
        try {
            $cache->get("anything");
        } catch (Framework\Cache\Exception\Service $e) {
            return ($cache instanceof Framework\Cache\Driver\Redis);
        }
        return false;
    },
    "Cache\Driver\Redis disconnects and returns itself",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache = $cache->connect();
        return ($cache->set("foo", "bar", 1) instanceof Framework\Cache\Driver\Redis);
    },
    "Cache\Driver\Redis sets values and returns itself",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache = $cache->connect();
        $cache->set("foo", "bar", 1);
        return ($cache->get("foo") == "bar");
    },
    "Cache\Driver\Redis retrieves values",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache = $cache->connect();
        return ($cache->get("404", "baz") == "baz");
    },
    "Cache\Driver\Redis returns default values",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache = $cache->connect();
        $cache->set("foo", "bar", 1);
        // we sleep to void the 1 second cache key/value above
        sleep(1);
        return ($cache->get("foo") == null);
    },
    "Cache\Driver\Redis expires values",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache = $cache->connect();
        $cache = $cache->set("hello", "world");
        $cache = $cache->erase("hello");
        return ($cache->get("hello") == null && $cache instanceof Framework\Cache\Driver\Redis);
    },
    "Cache\Driver\Redis erases values and returns itself",
    "Cache\Driver\Redis"
);

Framework\Test::add(
    function () {
        $cache = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache = $cache->initialize();
        $cache = $cache->connect();
        $cache = $cache->set("hello", "world");
        
        $cache2 = new Framework\Cache(array(
            "type" => "redis"
        ));
        $cache2 = $cache2->initialize();
        $cache2 = $cache2->connect();
        $cache2 = $cache2->set("hello", "world 2");
        return ($cache->get("hello") != $cache2->get("hello"));
    },
    "Cache\Driver\Redis use prefix to prevent duplicate key",
    "Cache\Driver\Redis"
);

$result = Framework\Test::run();
var_dump($result);
// var_dump($result['exceptions']);