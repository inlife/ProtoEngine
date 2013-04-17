<?php

class peArticle extends peModel
{ 
    public function save()
    {
        if ($this->date && $this->title && $this->text) {
            if (strlen($this->text) < 32 * 1000) {
                
                /* Saving image */
                $image = new peImage("image", 300 * 1000); // 300 kb
                if ($image->save()) {
                    $this->image = $image->getUrl();
                }
                /* Converting datetime to unix-timestamp (much easier to store and compare) */
                $this->date = strtotime($this->date);
                
                /* Deleting "id" field (not need to insert) */
                array_shift($this->_data);
                
                /* Running query */
                return $this->query()->insert()->table("articles")->values($this->_data)->run();
                
            } else {
                peHttp::error(6);
            }
        } else {
            peHttp::error(5);
        }
    }
    
    public function loadAll()
    {
        $articles = $this->query()->select()->table("articles")->order("id", true)->limit(5)->run();
        
        /* Finding time by timestampts */
        foreach($articles as $key => $article)
        {
            $articles[$key]->day   = date("d", $article->date);
            $articles[$key]->month = date("M", $article->date);
            $tags = explode(",", $article->tags);
            $pTags = array();
            foreach($tags as $tag)
            {
                $pTag = new peResponse();
                $pTag->text = $tag;
                $pTags[] = $pTag;
            }
            $articles[$key]->tags = $pTags;
        }
        return $articles;
    }
}