<?php

include_once __DIR__ . '/../autoload.php';



   $result = Framework\Test::run();
   var_dump($result);
   // var_dump($result['exceptions']);
   die();