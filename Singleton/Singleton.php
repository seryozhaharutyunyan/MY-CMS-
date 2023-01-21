<?php
namespace Singleton;

trait Singleton
{
    protected static $_instance;

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function __wakeup()
    {
        // TODO: Implement __wakeup() method.
    }

    public static function getInstance(){
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }
        self::$_instance = new self;
        if(method_exists(self::$_instance, 'connect')){
            self::$_instance->connect();
        }
        return self::$_instance;
    }

    public static function started(){// for model
        self::getInstance();

        if(isset(self::$_instance->with) && !empty(self::$_instance->with)){
            foreach (self::$_instance->with as $item){
                $item();
            }
        }

        return self::$_instance;
    }

}