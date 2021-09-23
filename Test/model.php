<?php

include_once __DIR__ . '/../autoload.php';

# We also test whether rows can be returned form the database, matching WHERE conditions.
$database = new Framework\Database(
    array(
        "type" => "mysql",
        "options" => array(
            "host" => "localhost",
            "username" => "root",
            "password" => "",
            "schema" => "prophpmvc"
        )
    )
);
$database = $database->initialize();
$database = $database->connect();
Framework\Registry::set("database", $database);
class Example extends Framework\Model
{
    /**
    * @readwrite
    * @column
    * @type autonumber
    * @primary
    */
    protected $_id;
    /**
     * @readwrite
     * @column
     * @type text
     * @length 32
     */
    protected $_name;
    /**
     * @readwrite
     * @column
     * @type datetime
     */
    protected $_created;
}

Framework\Test::add(
    # We need to test that the model structure syncs correctly to the database.
    function () use ($database) {
        $example = new Example();
        return ($database->sync($example) instanceof Framework\Database\Connector\Mysql);
    },
    "Model syncs",
    "Model"
);

Framework\Test::add(
    # We need to test whether rows can be inserted from the database. 
    function () use ($database) {
        $example = new Example(array(
            "name" => "foo",
            "created" => date("Y-m-d H:i:s")
        ));
        return ($example->save() > 0);
    },
    "Model inserts rows",
    "Model"
);



Framework\Test::add(
    function () use ($database) {
        return (Example::count() == 1);
    },
    "Model fetches number of rows",
    "Model"
);

   $result = Framework\Test::run();
var_dump($result);
// var_dump($result['exceptions']);
die();

   Framework\Test::add(
    function() use ($database)
    {
        
    $example = new Example(array(
    "name" => "foo",
    "created" => date("Y-m-d H:i:s")
    ));
    $example->save();
    $example->save();
    $example->save();
    return (Example::count() == 2);
    },
    "Model saves single row multiple times",
    "Model"
   );

   Framework\Test::add(
    function() use ($database)
    {

        # We need to test whether rows can be updated from the database. 
    $example = new Example(array(
    "id" => 1,
    "name" => "hello",
    "created" => date("Y-m-d H:i:s")
    ));
    $example->save();
    return (Example::first()->name == "hello");
    },
    "Model updates rows",
    "Model"
   );

   Framework\Test::add(
    function() use ($database)
    {

        # We need to test whether rows can be  deleted from the database. 
    $example = new Example(array(
    "id" => 2
    ));
    $example->delete();
    return (Example::count() == 1);
    },
    "Model deletes rows",
    "Model"
   );