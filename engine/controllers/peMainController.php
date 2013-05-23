<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peMainController
 *  @Project: Proto Engine 3
 */

class peMainController extends peController
{
    public static function indexAction()
    {
        /* Imports */
        peLoader::import("models.peArticle");
        
        /* Generating response*/ 
        $object = new peArticle();
        $response = new peResponse("index", true);
        $response->page->title = "Главная" . peProject::getTitle();
        $response->page->keywords = "";
        $response->page->description = "";
        $response->articles = $object->call("loadAll");
        return $response;
    }
}