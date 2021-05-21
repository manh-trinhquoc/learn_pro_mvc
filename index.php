<?php

function autoload($class)
{
    $paths = explode(PATH_SEPARATOR, get_include_path());

    $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE;
    $file = strtolower(str_replace("\\", DIRECTORY_SEPARATOR, trim($class, "\\"))) . ".php";

    foreach ($paths as $path)
    {
        $combined = $path . DIRECTORY_SEPARATOR . $file;

        if (file_exists($combined))
        {
            include($combined);
            return;
        }
    }

    throw new Exception("{$class} not found");
}

class Autoloader 
{
    public static function autoload($class)
    {
        autoload($class);
    }
}

spl_autoload_register('autoload');
spl_autoload_register(array('autoloader', 'autoload'));

var_dump(PREG_SPLIT_NO_EMPTY);
var_dump(PREG_SPLIT_DELIM_CAPTURE);
var_dump(DIRECTORY_SEPARATOR);
var_dump(get_include_path());
var_dump(PREG_PATTERN_ORDER);