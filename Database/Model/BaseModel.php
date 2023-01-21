<?php

namespace Database\Model;

abstract class BaseModel
{
    protected $table;
    protected $query;
    protected $db;
    protected $data = [];
    protected $with = [];

    public function get()
    {
        $query = $this->correctQuery();

        $data = $this->db->query($query)->fetch_assoc();
        if (isset($this->data) && ! empty($this->data)) {
            foreach ($this->data as $item) {
                if ($data['id'] === $item['id']) {
                    $data = $item;
                    break;
                }
            }
        }

        return $data;
    }

    public function getAll()
    {
        if (isset($this->data) && ! empty($this->data)) {
            return $this->data;
        }

        $query = $this->correctQuery();

        return $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO $this->table ";
        $str1  = '(';
        $str2  = '(';

        foreach ($data as $key => $item) {
            $str1 .= "$key,";
            $str2 .= "'$item',";
        }

        $query .= \rtrim($str1, ',') . ") VALUES " . rtrim($str2, ',') . ")";

        return $this->db->query($query);
    }

    public function update($data, $column, $value)
    {
        $query = "UPDATE $this->table SET ";
        $str   = "";

        foreach ($data as $key => $item) {
            $str .= "$key='$item',";
        }
        $where = " WHERE $column='$value'";
        $query .= \rtrim($str, ',') . $where;

        return $this->db->query($query);
    }

    public function updateOrCreate($data, $column, $value)
    {
        $model = $this->select()->where($column, $value)->get();
        if (isset($model) && ! empty($model)) {
            return $this->update($data, $column, $value);
        } else {
            return $this->create($data);
        }
    }

    public function delete($column, $value)
    {
        $query = "DELETE FROM $this->table WHERE $column = '$value'";

        return $this->db->query($query);
    }

    public function deleteAll()
    {
        $query = "DELETE FROM $this->table";

        return $this->db->query($query);
    }

    public function select($colums = '')
    {
        $query = "SELECT ";
        if (empty($colums)) {
            $query .= "*";
        } elseif (\is_string($colums)) {
            $query .= "`$colums`";
        } elseif (\is_array($colums)) {
            $query .= "(";
            foreach ($colums as $colum) {
                $query .= "$colum,";
            }
            $query = rtrim($query, ',');
            $query .= ') ';
        }
        $query       .= " FROM $this->table";
        $this->query = $query;

        return $this;
    }

    public function count($colum='*')
    {
        $this->query = "SELECT COUNT($colum) FROM $this->table";
        return $this;
    }

    public function where($column, $value, $option = '=')
    {
        if (\preg_match('/AND$/', $this->query)) {
            $query = " $column$option'$value' AND";
        } else {
            $query = " WHERE $column$option'$value' AND";
        }
        $this->query .= $query;

        return $this;
    }

    public function orWhere($column, $value, $option = '=')
    {
        $query = " OR $column$option'$value' AND";
        if (\preg_match('/AND$/', $this->query)) {
            $queryBest   = $this->correctQuery();
            $queryBest   .= $query;
            $this->query = $queryBest;
        } else {
            return;
        }

        return $this;
    }

    public function orderBy($column, $option = 'ASC')
    {
        $query = $this->correctQuery();

        $option      = \strtoupper($option);
        $query       .= " ORDER BY $column $option";
        $this->query = $query;

        return $this;
    }

    public function limit($limit = 1)
    {
        $query = $this->correctQuery();


        $query       .= " LIMIT $limit";
        $this->query = $query;

        return $this;
    }

    protected function correctQuery()
    {
        $query = \preg_replace('/\s+AND$/i', '', $this->query);

        return $query;
    }

    public function oneToOne($table, $thisKey, $key)
    {
        $query  = "SELECT $table.* FROM $this->table INNER JOIN $table ON $this->table.$thisKey = $table.$key";
        $data   = $this->select()->getAll();
        $dataTo = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        if (empty($table->data)) {
            $this->setData($data, $dataTo, $table, $thisKey, $key);
        } else {
            $this->setData($this->data, $dataTo, $table, $thisKey, $key);
        }
    }

    protected function setData($data, $dataTo, $table, $thisKey, $key, $flag = '')
    {
        foreach ($data as $k => $item) {
            foreach ($dataTo as $value) {
                if ($item[$thisKey] === $value[$key]) {
                    if ($flag === 'many') {
                        if (isset($value[$key])) {
                            unset($value[$key]);
                        }
                        $data[$k][$table][] = $value;
                    } else {
                        $data[$k][$table] = $value;
                    }
                }
            }
            if ( ! isset($data[$k][$table])) {
                if ($flag === 'many') {
                    $data[$k][$table][] = [];
                } else {
                    $data[$k][$table] = [];
                }
            }
        }
        $this->data = $data;
    }

    public function oneToMany($table, $thisKey, $key)
    {
        $query  = "SELECT $table.* FROM $this->table INNER JOIN $table ON $this->table.$thisKey = $table.$key";
        $data   = $this->select()->getAll();
        $dataTo = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        if (empty($table->data)) {
            $this->setData($data, $dataTo, $table, $thisKey, $key, 'many');
        } else {
            $this->setData($this->data, $dataTo, $table, $thisKey, $key, 'many');
        }
    }


    public function manyToMany($table, $tableCon, $foreingPivotKey, $relatedPivotKey)
    {
        $query  = "SELECT $table.*, $tableCon.$relatedPivotKey FROM $table 
            INNER JOIN $tableCon ON $tableCon.$foreingPivotKey=$table.id 
            INNER JOIN $this->table ON $this->table.id=$tableCon.$relatedPivotKey";
        $data   = $this->select()->getAll();
        $dataTo = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        if (empty($table->data)) {
            $this->setData($data, $dataTo, $table, 'id', $relatedPivotKey, 'many');
        } else {
            $this->setData($this->data, $dataTo, $table, 'id', $relatedPivotKey, 'many');
        }
        dd($this->data);
    }
}