<?php

namespace Database\Migration;

use mysqli;

abstract class Migration
{
    protected $table = '';
    protected $create = "";
    protected $db;

    /**
     *
     */
    public function __construct()
    {
        $className    = \explode('\\', \get_class($this));
        $this->table  = \strtolower($className[\count($className) - 1]);
        $this->create .="DROP TABLE $this->table; CREATE TABLE $this->table (";
    }

    /**
     * @return mixed
     */
    public abstract function start();
    public abstract function update();
    public abstract function drop();

    /**
     * @return $this
     */
    protected function get()
    {
        if(\preg_match('/\s+CREATE\s+TABLE\s+/', $this->create)){
            $this->create=\rtrim($this->create, ',');
            $this->create .= ');';
        }else{
            $this->create .= ';';
        }

        /*$this->connect();

        return $this->db->query($this->create);
        $this->db->close();*/

        return $this;
    }


    public function connect()
    {
        $this->db = new mysqli(\HOST, \USER, \PASSWORD, \DB_NAME);
        if ($this->db->connect_error) {
            die('Connect Error (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
        }
    }

    protected function id()
    {
        $this->create .= "id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,";

        return $this;
    }

    protected function varchar($name, $length)
    {
        $this->create .= " $name VARCHAR($length) NOT NULL,";

        return $this;
    }

    protected function text($name)
    {
        $this->create .= "$name TEXT NOT NULL,";

        return $this;
    }

    protected function int($name)
    {
        $this->create .= "$name INT NOT NULL,";

        return $this;
    }

    protected function bigInt($name)
    {
        $this->create .= "$name BIGINT NOT NULL,";

        return $this;
    }

    protected function usignetBigInt($name)
    {
        $this->create .= "$name BIGINT UNSIGNED NOT NULL,";

        return $this;
    }

    protected function tinyInt($name)
    {
        $this->create .= "$name TINYINT NOT NULL,";

        return $this;
    }

    protected function smallInt($name)
    {
        $this->create .= "$name SMALLINT NOT NULL,";

        return $this;
    }

    protected function mediumInt($name)
    {
        $this->create .= "$name MEDIUMINT NOT NULL,";

        return $this;
    }

    protected function float($name)
    {
        $this->create .= "$name FLOAT NOT NULL,";

        return $this;
    }

    protected function double($name)
    {
        $this->create .= "$name DOUBLE NOT NULL,";

        return $this;
    }

    protected function longText($name)
    {
        $this->create .= "$name LONGTEXT  NOT NULL,";

        return $this;
    }

    protected function date($name)
    {
        $this->create .= " $name DATE  NOT NULL,";

        return $this;
    }

    protected function dateTime($name)
    {
        $this->create .= "$name DATETIME  NOT NULL,";

        return $this;
    }

    protected function time($name)
    {
        $this->create .= "$name TIME  NOT NULL,";

        return $this;
    }

    protected function timeStamp($name)
    {
        $this->create .= "$name TIMESTAMP  NOT NULL,";

        return $this;
    }

    protected function json($name)
    {
        $this->create .= "$name JSON  NOT NULL,";

        return $this;
    }

    protected function enum($name, array $values)
    {
        $strQuery = "$name ENUM(";
        foreach ($values as $item) {
            $strQuery .= "$item,";
        }
        $strQuery     = \rtrim($strQuery, ',');
        $strQuery     .= " NOT NULL,";
        $this->create .= $strQuery;

        return $this;
    }

    protected function bool($name, array $values)
    {
        $this->create .= "$name BOOL  NOT NULL,";

        return $this;
    }


    protected function nullable()
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \preg_replace('/\s+?(NOT\s+?NULL),$/', ' NULL,', $this->create);

            return $this;
        }

        return $this;
    }

    protected function unique()
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \rtrim($this->create, ',');
            $this->create .= " UNIQUE,";

            return $this;
        }

        return $this;
    }

    protected function createAt()
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \preg_replace('/\s+?(NOT\s+?NULL),$/', " DEFAULT CURRENT_TIMESTAMP,", $this->create);

            return $this;
        }

        return $this;
    }

    protected function updateAt()
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \preg_replace('/\s+?(NOT\s+?NULL),$/',
                " DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", $this->create);

            return $this;
        }

        return $this;
    }

    protected function defaultValue($value)
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \preg_replace('/\s+?(NOT\s+?NULL),$/', " DEFAULT '$value',", $this->create);

            return $this;
        }

        return $this;
    }

    protected function foreingKey($table, $key, $foreingKey)
    {
        $this->create .= " FOREIGN KEY ($foreingKey) REFERENCES $table($key)";

        return $this;
    }

    protected function dropTable(){
        $this->create="DROP TABLE $this->table";

        return $this;
    }

    protected function addColumn(){
        $this->create="ALTER TABLE $this->table ADD ";

        return $this;
    }

    protected function dropColumn($name){
        $this->create="ALTER TABLE $this->table DROP COLUMN $name";

        return $this;
    }

    protected function index($columns){
        $name="idx_$this->table";
        if(\is_string($columns)){
            $name.="_$columns";
        }
        $this->create=\rtrim($this->create, ',');
        $this->create.="); CREATE INDEX $name ON $this->table (";
        if(\is_array($columns)){
            foreach ($columns as $column){
                $this->create.="$column,";
            }
            $this->create=\rtrim($this->create, ',');
        }else{
            $this->create.="$columns";
        }

        return $this;
    }

    protected function dropIndex($name){// name idx_table_column name
        $this->create='DROP INDEX $name ON $this->table';

        return $this;
    }
}