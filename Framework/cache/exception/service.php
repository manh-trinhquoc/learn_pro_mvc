<?php

namespace Framework\Cache\Exception ;

class Service extends \Framework\Core\Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Throwable $previous = null) {
        // some code
        // var_dump($message);
        // var_dump($this);
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}