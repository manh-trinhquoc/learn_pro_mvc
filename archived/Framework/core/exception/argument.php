<?php

namespace Framework\Core\Exception ;

class Argument extends \Framework\Core\Exception
{
    public function __construct($message, $code = 0, \Throwable $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        var_dump($this);
    }
}