<?php

use Framework\Inspector;

require_once 'autoload.php';


// class Hello extends Framework\Base
// {
//     /**
//      * @readwrite
//      */
//     protected $_world;

//     public $test;

//     public function setWorld($value)
//     {
//         echo "your setter is being called <br/>";
//         $this->_world = $value;
//     }
//     public function getWorld()
//     {
//         echo "your getter is being called! <br />";
//         return $this->_world;
//     }
// }

// $hello = new Hello();
// $hello->world = "foo!";
// $hello->test = "test!";

// var_dump($hello->world);
// var_dump($hello->test);

// $inspector = new Inspector('hello');
// var_dump($inspector);
// var_dump($inspector->getClassMeta());
// var_dump($inspector->getClassProperties());
// var_dump($inspector->getPropertyMeta('_world'));
// var_dump($inspector->getPropertyMeta('test'));
// var_dump($inspector->getClassMethods());
// var_dump($inspector->getMethodMeta('setWorld'));


// //tesst ussing the cache classes
// function getFriends() {
//     $cache = new \Framework\Core(array(
//         "type" => "memcached"
//     ));

//     $cache->initialize();
//     $friends = unserialize($cache->get("friends.{$user->id}"));
//     if (empty($friends)) {
//         //get friend from db
//         $cache->set("firends.{$user->id}", serialize($friends));
//     }

//     return $friends;
// }

// tesst ussing registry
// class Ford
// {
//     public $founder = "Henry Ford";
//     public $headquarter = "Detroit";
//     public $employees = 16400;

//     public function produces($car)
//     {
//         return $car->producer == $this;
//     }

//     private static $_instance;

//     private function __construct()
//     {
//         //do nothing
//     }

//     private function __clone()
//     {
//         //do nothing
//     }

//     public function instance()
//     {
//         if (!isset(self::$_instance))
//         {
//             self::$_instance = new self();
//         }
//         return;
//     }
// }
// class Car
// {
//     public $color;
//     public $producer;
// }

// $ford = new Ford();
// $car = new Car();
// $car->color = "Blue";
// $car->producer = $ford;

// echo $ford->produces($car);
// echo $ford->founder;

// // Framework\Registry::set("ford", new Ford());
// // $car = new Car();
// // $car->setColor("Blue")->setProcducer(Framework\Registry::get("ford"));

// // echo Framework\Registry::get('ford')->produces($car);
// // echo Framework\Registry::get('ford')->founder;
// class Home extends Framework\Controller
// {
//  public function index()
//  {
//  echo "here";
//  }
// }
// $router = new Framework\Router();
// $router->addRoute(
//  new Framework\Router\Route\Simple(array(
//  "pattern" => ":name/profile",
//  "controller" => "home",
//  "action" => "index"
//  ))
// );
// $router->url = "chris/profile";
// $router->dispatch();

class Index extends Framework\Controller
{
    /**
     * @once
     * @protected
     */
    public function init()
    {
        echo "init";
    }
    /**
     * @protected
     */
    public function authenticate()
    {
        echo "authenticate";
    }
    /**
     * @before init, authenticate, init
     * @after notify
     */
    public function home()
    {
        echo "hello world!";
    }
    /**
     * @protected
     */
    public function notify()
    {
        echo "notify";
    }
}
$router = new Framework\Router();
$router->addRoute(
    new Framework\Router\Route\Simple(array(
        "pattern" =>":name/profile",
        "controller" =>"home",
        "action" => "index"
    ))
);
$router-> url = "chris/profile";
$router-> dispatch();