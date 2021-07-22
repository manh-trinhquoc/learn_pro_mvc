<?php

namespace Framework;

use Framework\Base as Base;
use Framework\Events as Events;
use Framework\Registry as Registry;
use Framework\Inspector as Inspector;
use Framework\Router\Exception as Exception;

class Router extends Base {
    /**
     * @readwrite
     */
     protected $_url;
    
     /**
      * @readwrite
      */
      protected $_extension;

    /**
     * @read
     */
    protected $_controller;

    /**
     * @read
     */
    protected $_action;

    protected $_routes = array();

    public function _getExceptionForImplementation($method)
    {
        return new Exception\Implementation("{$method} method not implemented");
    }

    public function addRoute($route)
    {
        $this->_routes[] = $route;
        return $this;
    }

    public function removeRoute($route)
    {
        foreach ($this->_routes as $i => $stored)
        {
            if ($stored == $route)
            {
                unset($this->_routes[$i]);
            }
        }
        return $this;
    }

    public function getRoutes()
    {
        $list = array();
        foreach ($this->_routes as $route){
            $list[$route->pattern] = get_class($route);
        }

        return $list;
    }

    public function dispatch()
    {
        $url = $this->url;
        $parameters = array();
        $controller = "index";
        $action = "index";
    }

}