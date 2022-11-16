<?php
namespace Framework\Database\Connector;

use Framework\Database as Database;
use Framework\Database\Exception as Exception;

class Postgres extends Database\Connector
{
    protected $_service;
    /**
     * @readwrite
     */
    protected $_host;
    /**
     * @readwrite
     */
    protected $_username;
    /**
     * @readwrite
     */
    protected $_password;
    /**
     * @readwrite
     */
    protected $_schema;
    /**
     * @readwrite
     */
    protected $_port="3306";
    /**
     * @readwrite
     */
    protected $_isConnected = false;
    /**
     * @readwrite
     */
    protected $_options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    /**
     * @read
     */
    protected $_prepared = '';

    // checks if connected to the database
    protected function _isValidService()
    {
        $isEmpty = empty($this->_service);
        $isInstance = $this->_service instanceof \PDO;
        if ($this->isConnected && $isInstance && !$isEmpty) {
            return true;
        }
        return false;
    }
    // connects to the database
    public function connect()
    {
        if (!$this->_isValidService()) {
            // Query string
            $dsn = "pgsql:host={$this->_host};port={$this->_port};dbname={$this->_schema}";
            try {
                $myPdo = [
                    $dsn,
                    $this->_username,
                    $this->_password,
                    $this->_options
                ];
                // var_dump($myPdo);

                $myPdo = new \PDO(...$myPdo);
                // var_dump(get_class_methods($myPdo));
                // var_dump(get_object_vars($myPdo));
                $this->_service = $myPdo;
            } catch (\PDOException $e) {
                throw new Exception\Postgres($e->getMessage());
            } catch (\Exception $e) {
                throw new Exception\Service($e->getMessage());
            }
            $error_code = (int)$myPdo->errorCode();
            // var_dump($error_code);
            // var_dump($myPdo->errorInfo());
            // var_dump($myPdo->getAvailableDrivers());
            if ($error_code != 0) {
                throw new Exception\Service("Unable to connect to service");
            }
            $this->isConnected = true;
        }
        return $this;
    }

    // disconnects from the database
    public function disconnect()
    {
        if ($this->_isValidService()) {
            $this->isConnected = false;
            $this->_service = null;
        }
        return $this;
    }

    // returns a corresponding query instance
    public function query()
    {
        return new Database\Query\Postgres(array(
            "connector" => $this
        ));
    }

    // executes the provided SQL statement
    public function execute($sql)
    {
        if (!$this->_isValidService()) {
            throw new Exception\Service("Not connected to a valid service");
        }
        try {
            if (!$this->_prepared) {
                // var_dump('not prepared');
                $result = $this->_service->query($sql);
            }
        } catch (\PDOException $e) {
            $myPdo = $this->_service;
            $error_code = $myPdo->errorCode();
            var_dump($myPdo->errorInfo());
            // throw new Exception\Postgres($e->getMessage(), $error_code);
            $result = null;
        } catch (\Exception $e) {
            throw new Exception\Service($e->getMessage());
        }
        
        return $result;
    }

    // returns the ID of the last row to be inserted
    public function getLastInsertId()
    {
        if (!$this->_isValidService()) {
            throw new Exception\Service("Not connected to a valid service");
        }
        return $this->_service->lastInsertId();
    }

    // returns the number of rows affected by the last SQL query executed
    public function getAffectedRows()
    {
        if (!$this->_isValidService()) {
            throw new Exception\Service("Not connected to a valid service");
        }
        return $this->_service->affected_rows;
    }

    // returns the last error of occur
    public function getLastError()
    {
        if (!$this->_isValidService()) {
            throw new Exception\Service("Not connected to a valid service");
        }
        $lastError = $this->_service->errorInfo();
        if ($lastError[0] != '00000') {
            return $lastError;
        }
        return null;
    }
    
    public function sync($model) {
        $lines = array();
        $indices = array();
        $columns = $model->columns;
        $template = "CREATE TABLE '%s' (\n%s,\n%s\n) ENGINE=%s DEFAULT CHARSET=%s;";
        foreach ($columns as $column) {
            $raw = $column["raw"];
            $name = $column["name"];
            $type = $column["type"];
            $length = $column["length"];
            if ($column["primary"]) {
                $indices[] = "PRIMARY KEY ('{$name}')";
            }
            if ($column["index"]) {
                $indices[] = "KEY '{$name}' ('{$name}')";
            }

            switch ($type) {
                case "autonumber":
                    $lines[] = "'{$name}' int(11) NOT NULL AUTO_INCREMENT";
                    break;
                case "text":
                    if ($length !== null && $length <= 255) {
                        $lines[] = "'{$name}' varchar({$length}) DEFAULT NULL";
                    } else {
                        $lines[] = "'{$name}' text";
                    }
                    break;
                case "integer":
                    $lines[] = "'{$name}' int(11) DEFAULT NULL";
                    break;
                case "decimal":
                    $lines[] = "'{$name}' float DEFAULT NULL";
                    break;
                case "boolean":
                    $lines[] = "'{$name}' tinyint(4) DEFAULT NULL";
                    break;
                case "datetime":
                    $lines[] = "'{$name}' datetime DEFAULT NULL";
                    break;
            }
        }
        $table = $model->table;
        $sql = sprintf(
            $template,
            $table,
            join(",\n", $lines),
            join(",\n", $indices),
            $this->_engine,
            $this->_charset
        );
        $result = $this->execute("DROP TABLE IF EXISTS {$table};");

        if ($result === false) {
            $error = $this->lastError;
            throw new Exception\Sql("There was an error in the query: {$error}");
        }

        $result = $this->execute($sql);
        if ($result === false) {
            $error = $this->lastError;
            throw new Exception\Sql("There was an error in the query: {$error}");
        }
        return $this;
    }
}