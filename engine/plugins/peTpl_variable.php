<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTpl_variable
 *  @Project: Proto Engine 3
 */

class peTpl_variable
{
    public static function plugin()
    {
        peStorage::add(peTpl_Plugins, __CLASS__, true);
    }
    
    public static $blocked = false;
    public static $syntax = array(
        "([A-Za-z0-9._]{2,64})"
    );
    
    public static function setBlock($value)
    {
        self::$blocked = $value;
    }
    
    public static function addIgnore($string)
    {
        self::$syntax[$string] = $string;
    }
    
    public static function getIgnore($string)
    {
        if (isset(self::$syntax[$string])) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function delIgnore($string)
    {
        unset(self::$syntax[$string]);
    }
    
    public static function syntax(&$tpl, $n, &$data, $ignore = false)
    {
        if (!self::$blocked) {
            $matches = array();
            if (preg_match(peTemplate::exp(self::$syntax[0]), $tpl[$n], $matches)) {
                if (!strpos($matches[0], peTpl_NoCacheSym) && !in_array($matches[1], self::$syntax) 
                    && (strpos($matches[1], ".") || $ignore)) {
                    list($first) = explode(".", $matches[1]);
                    if ($ignore || !in_array($first, self::$syntax)) {
                        $route = str_replace(".", "->", $matches[1]);
                        $value = eval('return @$data->' . $route . ';');
                        if ($value instanceof peModel) $value = $value->_recall();
                        if ($ignore) return $value;
                        if (empty($value)) $value = null;
                        $tpl[$n] = preg_replace(
                            peTemplate::exp($matches[1]),
                            $value, $tpl[$n]
                        );
                    }
                }
            }
        }
    }
}