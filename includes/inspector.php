<?php

namespace Framework;

class Inspector
{
    protected $_class;
    protected $_meta = array(
        "class" => array(),
        "properties" => array(),
        "methods" => array()
    );
    protected $_properties = array();
    protected $_methods = array();

    public function __construct($class);
    {
        $this->_class = $class;
    }

    protected function _getClassComment()
    {
        $reflection = new \ReflectionClass($this->_class);
        return $reflection->getDocComment();
    }

}