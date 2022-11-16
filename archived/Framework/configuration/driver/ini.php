<?php
namespace Framework\Configuration\Driver;

use Framework\ArrayMethods as ArrayMethods;
use Framework\Configuration as Configuration;
use Framework\Configuration\Exception as Exception;

class Ini extends Configuration\Driver
{
    public function parse($path)
    {
        if (!file_exists($path . '.ini')) {
            throw new Exception\Argument("\$path argument is invalid: " . $path);
        }
        if (!isset($this->_parsed[$path])) {
            $config = array();
            ob_start();
            include("{$path}.ini");
            $string = ob_get_contents();
            ob_end_clean();
            $pairs = parse_ini_string($string);
            if ($pairs == false) {
                throw new Exception\Syntax("Could not parse configuration file");
            }
            foreach ($pairs as $key => $value) {
                $config = $this->_pair($config, $key, $value);
            }
            $this->_parsed[$path] = ArrayMethods::toObject($config);
        }
        return $this->_parsed[$path];
    }

    protected function _pair($config, $key, $value)
    {
        // var_dump($key);
        // var_dump(strstr($key, "."));
        if (strstr($key, ".")) {
            $parts = explode(".", $key, 2);
            // var_dump($parts);
            if (empty($config[$parts[0]])) {
                $config[$parts[0]] = array();
            }
            $config[$parts[0]] = $this->_pair($config[$parts[0]], $parts[1], $value);
        } else {
            $config[$key] = $value;
        }
        return $config;
    }
}