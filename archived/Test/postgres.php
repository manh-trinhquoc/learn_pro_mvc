<?php
include_once __DIR__ . '/../autoload.php';

var_dump(get_class_methods('\PDO'));

Framework\Test::add(
    // The Database factory class can be created.
    function () {
        $database = new Framework\Database();
        return ($database instanceof Framework\Database);
    },
    "Database factory class instantiates in uninitialized state",
    "Database"
);

$options = array(
    "type" => "postgres",
    "options" => array(
        "host" => "localhost",
        "username" => "postgres",
        "password" => "example",
        "schema" => "prophpmvc",
        "port" => "5432"
    )
);

var_dump($options);

Framework\Test::add(
    // The Database\Connector\Postgres class can initialize.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        return ($database instanceof Framework\Database\Connector\Postgres);
    },
    "Database\Connector\Postgres initializes",
    "Database\Connector\Postgres"
);

Framework\Test::add(
    // The Database\Connector\Postgres class can connect and return itself.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        return ($database instanceof Framework\Database\Connector\Postgres);
    },
    "Database\Connector\Postgres can connects and returns self",
    "Database\Connector\Postgres"
);

Framework\Test::add(
    // The Database\Connector\Postgres class can disconnect and return itself.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $database = $database->disconnect();
        try {
            $database->execute("SELECT 1");
        } catch (Framework\Database\Exception\Service $e)  {
            return ($database instanceof Framework\Database\Connector\Postgres);
        }
        return false;
    },
    "Database\Connector\Postgres disconnects and returns self",
    "Database\Connector\Postgres"
);

Framework\Test::add(
    // The Database\Connector\Postgres class can returns last sql error.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $database->execute("
            SOME INVALID SQL
        ");
        $lastError = $database->lastError;
        return (bool) $lastError;
    },
    "Database\Connector\Postgres can returns last sql error",
    "Database\Connector\Postgres"
);

Framework\Test::add(
    // The Database\Connector\Postgres class can execute SQL queries.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $sql = 'DROP TABLE IF EXISTS "tests";' ;
        // var_dump($sql);
        $database->execute($sql);
        // var_dump("lastError: " . $database->lastError);
        $sql = '
            CREATE TABLE IF NOT EXISTS tests (
                "id" SERIAL PRIMARY KEY,
                "number" INTEGER NOT NULL,
                "text" CHARACTER VARYING(255) NOT NULL,
                "boolean" boolean NOT NULL
            );
        ';
        // var_dump($sql);
        $database->execute($sql);
        // var_dump("lastError: " . $database->lastError);
        return !$database->lastError;
    },
    "Database\Connector\Postgres executes queries",
    "Database\Connector\Postgres"
);

Framework\Test::add(
    // The Database\Connector\Postgres class can return the last inserted ID.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $sql = "
            INSERT INTO tests (number, text, boolean) VALUES ('1337', 'text', '0');
        ";
        var_dump($sql);
        for ($i = 0; $i < 4; $i++) {
            $database->execute($sql);
        }
        // var_dump($database->lastInsertId);
        return $database->lastInsertId;
    },
    "Database\Connector\Postgres returns last inserted ID",
    "Database\Connector\Postgres"
);

$result = Framework\Test::run();
var_dump($result);
// var_dump($result['exceptions']);

Framework\Test::add(
    // The Database\Connector\Postgres class can return the number of affected rows.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $database->execute("
            UPDATE tests SET number = 1338;
        ");
        return $database->affectedRows;
    },
    "Database\Connector\Postgres returns affected rows",
    "Database\Connector\Postgres"
);

// The Database\Connector\Postgres class can return the last SQL error.

/**
 * Because we are working with both connectors and queries, 
 * we need to check the associations between the two
 */
Framework\Test::add(
    // The Database\Connector\Postgres class can return a Database\Query\Postgres instance.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $query = $database->query();
        return ($query instanceof Framework\Database\Query\Postgres);
    },
    "Database\Connector\Postgres returns instance of Database\Query\Postgres",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class references a connector.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $query = $database->query();
        $connector = $query->connector;
        // var_dump($connector === $database);
        return ($connector instanceof Framework\Database\Connector\Postgres);
    },
    "Database\Query\Postgres references connector Database\Connector\Postgres",
    "Database\Query\Postgres"
);
//end check associtations

Framework\Test::add(
    // The Database\Query\Postgres class can fetch the first row in a table.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $row = $database->query()
            ->from("tests")
            ->first();
        return ($row["id"] == 1);
    },
    "Database\Query\Postgres fetches first row",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can fetch multiple rows in a table.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $rows = $database->query()
            ->from("tests")
            ->all();
        return (sizeof($rows) == 4);
    },
    "Database\Query\Postgres fetches multiple rows data",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can get the number of rows in a table.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $count = $database
            ->query()
            ->from("tests")
            ->count();
        return ($count == 4);
    },
    "Database\Query\Postgres fetches number of rows in a table",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can use limit, offset, order, and direction.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $rows = $database->query()
            ->from("tests")
            ->limit(1, 2)
            ->order("id", "desc")
            ->all();
        return (sizeof($rows) == 1 && $rows[0]["id"] == 3);
    },
    "Database\Query\Postgres accepts LIMIT, OFFSET, ORDER and DIRECTION clauses",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can use multiple WHERE clauses.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $rows = $database->query()
            ->from("tests")
            ->where("id != ?", 1)
            ->where("id != ?", 3)
            ->where("id != ?", 4)
            ->all();
        return (sizeof($rows) == 1 && $rows[0]["id"] == 2);
    },
    "Database\Query\Postgres accepts WHERE clauses",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can specify and alias fields.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $rows = $database->query()
            ->from("tests", array(
                "id" => "foo"
            ))
            ->all();
        return (sizeof($rows) && isset($rows[0]["foo"]) && $rows[0]["foo"] == 1);
    },
    "Database\Query\Postgres can alias fields",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can join tables and alias joined fields.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $rows = $database->query()
            ->from("tests", array(
                "tests.id" => "foo"
            ))
            ->join("tests AS baz", "tests.id = baz.id", array(
                "baz.id" => "bar"
            ))
            ->all();
        return (sizeof($rows) && $rows[0]['foo'] == $rows[0]['bar']);
    },
    "Database\Query\Postgres can join tables and alias joined fields",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can insert rows.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $result = $database->query()
            ->from("tests")
            ->save(array(
                "number" => 3,
                "text" => "foo",
                "boolean" => true
            ));
        return ($result == 5);
    },
    "Database\Query\Postgres can insert rows",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can update rows.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $result = $database->query()
            ->from("tests")
            ->where("id = ?", 5)
            ->save(array(
                "number" => 3,
                "text" => "foo",
                "boolean" => false
            ));
        return ($result == 0);
    },
    "Database\Query\Postgres can update rows",
    "Database\Query\Postgres"
);

Framework\Test::add(
    // The Database\Query\Postgres class can delete rows.
    function () use ($options) {
        $database = new Framework\Database($options);
        $database = $database->initialize();
        $database = $database->connect();
        $database->query()
            ->from("tests")
            ->delete();
        return ($database->query()->from("tests")->count() == 0);
    },
    "Database\Query\Postgres can delete rows",
    "Database\Query\Postgres"
);