<?php

namespace Framework;

use Framework\Base as Base;
use Framework\Core\Exception as Exception;

class Configuration extends Base
{
    /**
     * @readwrite
     */
    protected $_type;
    /**
     * @readwrite
     */
    protected $options;
    
    protected function _getExceptionForImplementation($method)
    {
        return new Exception\Implementation("{$method} method not implemented");
    }

    public function initialize()
    {
        if (!$this->type) {
            throw new Exception\Argument("Invalid type");
        }

        switch ($this->type) {
            case "ini":
                return new Configuration\Driver\Ini($this->options);
                break;
            case "json":
                return new Configuration\Driver\Json($this->options);
                break;
            default:
                throw new Exception\Argument("Invalid type");
                break;
        }
    }
}