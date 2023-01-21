<?php

namespace Session;

class Session
{
    /**
     * @param   string  $name
     *
     * @return mixed
     */
    public static function get(string $name){
        return $_SESSION[$name];
    }

    /**
     * @param   string  $name
     * @param           $value
     *
     * @return bool
     */
    public static function set(string $name, $value)
    {
        $_SESSION[$name]=$value;
        return true;
    }
}