<?php

namespace Response;

class Response
{

    public static function responseJson($data){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    public static function response($data){
        header('Content-Type: text/html; charset=utf-8');
        echo $data;
    }

}