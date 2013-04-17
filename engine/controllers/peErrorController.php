<?php

class peErrorController
{
    public static function indexAction()
    {
        $param = new peRequest("code:i", "back:i");
        $title = "Ошибка";
        $message = "";
        switch($param->code) 
        {
            case 5: $message = "Заполнены не все поля"; break;
            case 6: $message = "Размер текста статьи больше 32кб"; break;
            case 7: $message = "Размер изображения больше 300кб"; break;
        }
        $response = new peResponse("error");
        $response->page->title = peProject::getTitle() . "Ошибка";
        $response->error->title = $title;
        $response->error->message = $message;
        return $response;
    }
}