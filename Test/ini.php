<?php

include_once __DIR__ . '/../autoload.php';

Framework\Test::add(
    function () {
        $configuration = new Framework\Configuration();
        return ($configuration instanceof Framework\Configuration);
    },
    "Configuration factory class can be created",
    "Configuration"
);

Framework\Test::add(
    function () {
        $configuration = new Framework\Configuration(array(
            "type" => "ini"
        ));
        $configuration = $configuration->initialize();
        return ($configuration instanceof Framework\Configuration\Driver\Ini);
    },
    "Configuration\Driver\Ini class can initialize",
    "Configuration\Driver\Ini"
);

Framework\Test::add(
    function () {
        $configuration = new Framework\Configuration(array(
            "type" => "ini"
        ));
        $configuration = $configuration->initialize();
        $parsed = $configuration->parse(__DIR__ . "/_test-configuration");
        // var_dump($configuration);
        // var_dump($parsed);
        // var_dump($parsed->config->first);
        return ($parsed->config->first == "hello" && $parsed->config->second->second == "bar");
    },
    "Configuration\Driver\Ini parses configuration files",
    "Configuration\Driver\Ini"
);

$result = Framework\Test::run();
var_dump($result);
// var_dump($result['exceptions']);