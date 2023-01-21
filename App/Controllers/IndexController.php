<?php

namespace App\Controllers;


use App\Migration\Users;
use App\Models\User;
use Database\Model\Model;
use Response\Response;

class IndexController extends Controller
{
    public function __invoke()
    {
        $query=bin2hex(random_bytes(32));


        dd($query);
    }

}