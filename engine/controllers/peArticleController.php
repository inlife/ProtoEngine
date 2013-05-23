<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peArticleController
 *  @Project: Proto Engine 3
 */

class peArticleController extends peController
{
    public static function indexAction()
    {
        $response = new peResponse("add_article");
        $response->page->title = "Добавление новости" . peProject::getTitle();
        return $response;
    }
    
    public static function addAction()
    {
        /* Imports */
        peLoader::import("models.peArticle");
        
        /* Generating response*/ 
        $params = new peRequest("title:200", "date", "text:h", "tags:t");
        $article = new peArticle();
        $article->copy($params);
        $article->save();
        self::redirect(peProject::getHost());
    }
}