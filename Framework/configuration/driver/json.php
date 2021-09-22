<?php
namespace Framework\Configuration\Driver;

use Framework\ArrayMethods as ArrayMethods;
use Framework\Configuration as Configuration;
use Framework\Configuration\Exception as Exception;

class Json extends Configuration\Driver
{
    public function parse($path)
    {
        if (!file_exists($path . '.json')) {
            throw new Exception\Argument("\$path argument is invalid: " . $path);
        }
        if (!isset($this->_parsed[$path])) {
            $config = array();
            ob_start();
            include("{$path}.json");
            $string = ob_get_contents();
            ob_end_clean();
            $pairs = json_decode($string);
            if ($pairs == false) {
                throw new Exception\Syntax("Could not parse configuration file");
            }
            $this->_parsed[$path] = $pairs;
        }
        return $this->_parsed[$path];
    }
}