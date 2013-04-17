<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTpl_extend
 *  @Project: Proto Engine 3
 */

class peTpl_extend
{
    public static function plugin()
    {
        peStorage::add(peTpl_Plugins, __CLASS__, true);
    }
    
    public static $syntax = "extends\s{0,5}\(\s{0,5}(\S{1,64})\s{0,5}\)";
    
    public static function syntax(&$tpl, $n)
    {
        $matches = array();
        if (preg_match(peTemplate::exp(self::$syntax), $tpl[$n], $matches)) {
            $tpl[$n] = preg_replace(
                peTemplate::exp(self::$syntax),
                peTemplate::call($matches[1]), $tpl[$n]
            );
        }
    }
    
}