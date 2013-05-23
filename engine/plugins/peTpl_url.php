<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTpl_url
 *  @Project: Proto Engine 3
 */

class peTpl_url
{
    public static function plugin()
    {
        peStorage::add(peTpl_Plugins, __CLASS__, true);
    }
    
    public static $syntax = "url\(\s{0,5}(\S{1,64})\s{0,5}\)";
    
    public static function syntax(&$tpl, $n, &$data)
    {
        $matches = array();
        if (preg_match(peTemplate::exp(self::$syntax), $tpl[$n], $matches)) {
            if (!strpos($matches[0], peTpl_NoCacheSym)) {
                $params = explode("::", $matches[1]);
                if (empty($params[0])) $params[0] = peTpl_DefaultPage;
                if (empty($params[1])) $params[1] = peTpl_DefaultAction;
                $url = peHttp::url(
                    array(
                        "name" => $params[0], 
                        "action" => $params[1]
                    )
                );

                $tpl[$n] = preg_replace(
                    peTemplate::exp(str_replace("(\S{1,64})", $matches[1], self::$syntax)),
                    $url, $tpl[$n]
                );
            }
        }
    }
}