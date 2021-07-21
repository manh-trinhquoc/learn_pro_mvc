<?php

namespace Framework\Router;

use Exception;

class Route extends Base
{
    /**
     * @readwrite
     */

     protected $_pattern;

     /**
      * @readwrite
      */

    protected $_controller;

    /**
     * @readwrite
     */

     protected $_action;

     /**
      * @readwrite
      */

    protected $_paramenters = array();

    public function _getExceptionForImplementation($method)
    {
        return new Exception\Implementation("{$method} method is not implemented");
    }
}