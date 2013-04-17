<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peStorage
 *  @Project: Proto Engine 3
 */

abstract class peStorage
{
    private static $_data = array();

    public static function add($name, $value, $concat = false)
    {
        if (!empty(self::$_data[$name]) && $concat) {
            if (!is_array(self::$_data[$name])) {
                self::$_data[$name] = array(self::$_data[$name]);
            }
            self::$_data[$name][] = $value;
        } else {
            self::$_data[$name] = $value;
        }
    }
    
    public static function get($name)
    {
        if (!empty(self::$_data[$name])) {
            return self::$_data[$name];
        } else {
            return null;
        }
    }
    
    public static function delete($name)
    {
        $data = self::get($name);
        unset(self::$_data[$name]);
        return $data;
    }
}