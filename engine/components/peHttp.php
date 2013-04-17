<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peHttp
 *  @Project: Proto Engine 3
 */

class peHttp 
{ 
    public static function url() 
    {
        $args = func_get_args();
        if (count($args) > 1) {
            $host = array_pop($args);
        } else {
            $host = peProject::getHost();
        }
        if (count($args) < 1) {
            $query = null;
        } else {
            $query = http_build_query(array_shift($args));
        }
        return $host . "?" . $query;
    }
    
    public static function redirect($url = null)
    {
        if ($url) {
            header(sprintf("Location: %s", $url));
            die();
        }
        return false;
    }
    
    public static function htmlRedirect($url = null, $time = 0) 
    {
        if (!$url) {
            $url = peProject::getHost();
        }
        print(
            sprintf(
                "<meta http-equiv='refresh' content='%d; URL=%s'>", $time, $url
            )
        );
        
    }
    
    public static function error($code = 0, $back = false)
    {
        self::redirect(
            self::url(
                array("name" => "error", "code" => $code, "back" => $back)
            )
        );
    }
}