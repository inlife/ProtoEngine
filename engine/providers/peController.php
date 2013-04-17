<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peController
 *  @Project: Proto Engine 3
 */

abstract class peController extends peHttp
{
    public static function getData()
    {
        /* Hooks */
        peHook::onGetData();
        $Page = new peRequest("name", "action");
        if (!@$Page->Name) $Page->Name = peTpl_DefaultPage;
        if (!@$Page->Action) $Page->Action = peTpl_DefaultAction;
        return self::classRegistry(
            $Page->Name,
            $Page->Action
        );
    }
    
    protected static function classRegistry($Name, $Method)
    {
        $class = peClass_Prefix . ucfirst(strtolower($Name)) . peTpl_ControllerPostfix;
        
        $method = strtolower($Method) . peTpl_ActionPostfix; 
        
        if (peLoader::import("controllers." . $class)) {
            if (class_exists($class) && is_callable($class . "::" . $method)) {
                return $class::$method();
            }
        }
        peHttp::error(404); 
    }
}
