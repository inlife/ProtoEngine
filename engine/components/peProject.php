<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peProject
 *  @Project: Proto Engine 3
 */

class peProject 
{
    protected static $_settings;
    private static $_lowered = false;
    
    public static function __callStatic($name, $args) 
    {
        self::tolower();
        switch(substr($name, 0, 3)) 
        {
            case "get": 
                if ($result = @self::$_settings[strtolower(substr($name, 3))]) {
                    return $result;
                } else {
                    return null;
                }
            break;
            case "set": 
                if (@self::$_settings[strtolower(substr($name, 3))] = $args[0]) {
                    return true;
                } else {
                    return false;
                }
            break;
        }
    }
    
    public static function load($params)
    {
        self::$_settings = $params;
    }
    
    private static function tolower()
    {
        if (!self::$_lowered) {
            self::$_lowered = true;
            self::$_settings = array_change_key_case(self::$_settings, CASE_LOWER);
        }
    }
}