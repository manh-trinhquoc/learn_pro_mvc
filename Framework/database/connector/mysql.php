<?php
namespace Framework\Database\Connector;

use Framework\Database as Database;
 use Framework\Database\Exception as Exception;

class Mysql extends Database\Connector
{
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