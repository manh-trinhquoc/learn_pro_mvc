<?php

namespace Framework;

use Framework\Base as Base;
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
        $numargs = func_get_args();
        // var_dump($numargs);
        // return new Exception\Implementation("{$method} medthod not implepemented");
        $class_methods = get_class_methods('Framework\Core\Exception\Implementation');
        var_dump($class_methods);
        var_dump('before throw CoreException\Implementation ' . $method);
        return new CoreException\Implementation("{$method} medthod not implepemented");
        var_dump('after throw CoreException\Implementation ' . $method);
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