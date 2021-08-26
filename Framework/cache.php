<?php

namespace Framework;

use Framework\Base as Base;
use Framework\Cache\Exception as Exception;

class Cache extends Base {
    /**
     * @readwrite
     */
    protected $_type;
    /**
     * @readwrite
     */
    protected $_options;

    protected function _getExceptionForImplementation($method)
    {
        return new Exception\Implementation("{$method} medthod not implepemented");
    }

    public function initialize() {
        if (!$this->type) {
            throw new Exception\Argument("Invalid type");
        }
        switch ($this->type) {
            case "memcached":
                $memcache = new Cache\Driver\Memcached($this->options);
                $memcache = $memcache->initialize();
                return $memcache;
            default:
                throw new Exception\Argument("Invalid type");
                return;
        }
    }
}