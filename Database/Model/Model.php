<?php

namespace Database\Model;

use mysqli;
use Singleton\Singleton;

class Model extends BaseModel //children use Singleton;
{
    public function connect()
    {
        $this->db = new mysqli(\HOST, \USER, \PASSWORD, \DB_NAME);
        if ($this->db->connect_error) {
            die('Connect Error (' . $this->db->connect_errno . ') ' . $this->db->connect_error);
        }
    }
}