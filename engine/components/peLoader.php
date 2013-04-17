<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peLoader
 *  @Project: Proto Engine 3
 */

class peLoader
{
    public static function import($name)
    {
        return peRequire(pePath_Engine . str_replace(".", ds, $name) . ".php");
    }
}