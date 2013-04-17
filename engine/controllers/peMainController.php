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
        
        /* Generating response*/ 
        $response = new peResponse("index");
        $response->page->title = "Главная" . peProject::getTitle();
        $response->page->keywords = "index,main,buddha,fashion,sushi,cafe";
        $response->page->description = "Buddha - Fashion Sushi Cafe";
        $response->page->m1 = 'class="current-menu-item"';
        return $response;
    }
    
    public static function newsAction()
    {
        $response = new peResponse("newslist");
        $response->page->title = "Новости" . peProject::getTitle();
        $response->page->m2 = 'class="current-menu-item"';
        return $response;
    }
}