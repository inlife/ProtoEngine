<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peHook
 *  @Project: Proto Engine 3
 */

abstract class peHook
{
    private static $_hooks = array();
    
    public static function addHook($event, $function)
    {
        self::$_hooks[$event][] = $function;
    }
    
    public static function __callStatic($name, $arguments) 
    {
        foreach(@(array)self::$_hooks[$name] as $function)
            $function($arguments);
    }
}