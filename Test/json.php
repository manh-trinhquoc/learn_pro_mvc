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
            "type" => "json"
        ));
        $configuration = $configuration->initialize();
        return ($configuration instanceof Framework\Configuration\Driver\Json);
    },
    "Configuration\Driver\Json class can initialize",
    "Configuration\Driver\Json"
);

Framework\Test::add(
    function () {
        $configuration = new Framework\Configuration(array(
            "type" => "json"
        ));
        $configuration = $configuration->initialize();
        $parsed = $configuration->parse(__DIR__ . "/_test-configuration-json");
        // var_dump($configuration);
        // var_dump($parsed);
        // var_dump($parsed->config->first);
        return ($parsed->config->first == "hello" && $parsed->config->second->second == "bar");
    },
    "Configuration\Driver\Json parses configuration files",
    "Configuration\Driver\Json"
);

$result = Framework\Test::run();
var_dump($result);
// var_dump($result['exceptions']);