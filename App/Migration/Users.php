<?php

namespace App\Migration;

use Database\Migration\Migration;

class Users extends Migration
{
    public function start()
    {
        return $this->id()
                    ->varchar('name', 255)
                    ->index('name')
                    ->get();

    }

    public function update()
    {
        return $this->dropColums('name')->get();
    }

    public function drop()
    {
        return $this->dropTable()->get();
    }
}