<?php

namespace Framework\Cache\Driver;

use Framework\Cache as Cache;
use Framework\Cache\Exception as Exception;
use Framework\StringMethods as StringMethods;

/**
 *  
 */

class Redis extends Cache\Driver {
    protected $_service;
    /**
     * @readwrite
     */
    protected $_host="127.0.0.1";

    /**
     * @readwrite
     */
    protected $_port = "6379";

    /**
     * @readwrite
     */
    protected $_isConnected = false;

    /**
     * @readwrite
     */
    protected $_prefix = null;

    protected function _isValidService() {
        $isEmpty = empty($this->_service);
        $isInstance = $this->_service instanceof \Redis;

        if ($this->isConnected && $isInstance && !$isEmpty) {
            return true;
        }

        return false;
    }

    public function connect() {
        try {
            $this->_service = new \Redis();
            $this->_service->connect(
                $this->host,
                $this->port
            );
            $this->isConnected = true;
        } catch (\Exception $e) {
            throw new Exception\Service("Unable to connect to service");
        }

        return $this;
    }

    public function disconnect() {
        if ($this->_isValidService()) {
            $this->_service->close();
            $this->isConnected = false;
        }
        return $this;
    }

    public function get($key, $default = null) {
        if (!$this->_isValidService()) {
            throw new Exception\Service("Not connected to a valid service");
        }
        $value = $this->_service->get($this->_prefix . $key);
        if ($value) {
            return $value;
        }
        return $default;
    }

    public function set($key, $value, $duration = 120) {
        if (!$this->_isValidService()) {
            throw new Exception\Service("Not connected to a valid service");
        }
        
        $this->_service->set($this->_prefix . $key, $value, $duration);
        return $this;
    }

    public function erase($key)
    {
        if (!$this->_isValidService()) {
            throw new Exception\Service("Not connect to a valid service");
        }

        $this->_service->delete($this->_prefix . $key);
        return $this;
    }

    public function initialize()
    {
        $this->_prefix = StringMethods::generateRandomString(4);
        return parent::initialize();
    }
}