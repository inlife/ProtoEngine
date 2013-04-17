<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: project
 *  @Project: Proto Engine 3
 */

require("core.php");

peCore::init(
    array(
        /* Basic project settings */
        "title"     => " | Buddha - Fashion Sushi Cafe",
        "host"      => "http://localhost:8080/proto/",
        "siteTheme" => "buddha",
        "tplDirs"   => array("style", "css", "script", "js", "images"),
        "debug"     => true,
        "hashSalt"  => "qw123",
        
        /* Mysql settings */
        "mysqlHost" => "localhost",
        "mysqlUser" => "root",
        "mysqlPass" => "usbw",
        "mysqlName" => "proto",
        
        /* Components */
        "components" => array(
            "components.peStorage",
            "components.peHttp",
            "components.peFile",
            "components.peImage",
            "components.peModel",
            "components.peQuery",
            "components.peResponse"
        ),
        /* Charsets */
        "charsetIn" => "utf-8",
        "charsetTo" => "utf-8" 
    )
);