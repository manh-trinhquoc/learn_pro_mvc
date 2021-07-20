<?php

use Framework\Inspector;

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

var_dump($hello->world);
var_dump($hello->test);

$inspector = new Inspector('hello');
var_dump($inspector);
var_dump($inspector->getClassMeta());
var_dump($inspector->getClassProperties());
var_dump($inspector->getPropertyMeta('_world'));
var_dump($inspector->getPropertyMeta('test'));
var_dump($inspector->getClassMethods());
var_dump($inspector->getMethodMeta('setWorld'));


//tesst ussing the cache classes
function getFriends() {
    $cache = new \Framework\Core(array(
        "type" => "memcached"
    ));

    $cache->initialize();
    $friends = unserialize($cache->get("friends.{$user->id}"));
    if (empty($friends)) {
        //get friend from db
        $cache->set("firends.{$user->id}", serialize($friends));
    }

    return $friends;
}