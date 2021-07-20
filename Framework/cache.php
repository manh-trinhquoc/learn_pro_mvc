<?php

namespace Framework;

use Framework\Base as Base;
use Framework\Cache as Cache;
use Framework\Cache\Exception as Exception;
use Framework\Core\Exception as CoreException;

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
            case "memcached": {
                return new Cache\Driver\Memcached($this->options);
                return;
            }
            default: {
                throw new CoreException\Argument("Invalid type");
                return;
            }
        }
    }
}