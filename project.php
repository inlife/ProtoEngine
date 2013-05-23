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
        "title"     => " | Proto Engine",
        "host"      => "http://localhost/",
        "siteTheme" => "proto_test",
        "tplDirs"   => array("style", "css", "script", "js", "images"),
        "debug"     => true,
        "hashSalt"  => "qw123",
        "charset"   => "utf-8",
        
        /* Mysql settings */
        "mysqlHost" => "localhost",
        "mysqlUser" => "user",
        "mysqlPass" => "pass",
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
        )
    )
);
