<?php

require_once 'autoload.php';
class Hello extends Framework\Base
{
    /**
     * @readwrite
     */
    protected $_world;

    public $test;

    public function setWorld($value)
    {
        echo "your setter is being called <br/>";
        $this->_world = $value;
    }
    public function getWorld()
    {
        echo "your getter is being called! <br />";
        return $this->_world;
    }
}

$hello = new Hello();
$hello->world = "foo!";
$hello->test = "test!";

echo $hello->world;
echo $hello->test;