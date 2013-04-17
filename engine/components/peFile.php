<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peFile
 *  @Project: Proto Engine 3
 */

class peFile
{
    public $filename;
    public $content;
    public $flags = 0;
    
    public function __construct($filename = null, $inArray = false) 
    {
        if ($filename) {
            $this->getFileName($filename);
            return $this->load($this->filename, $inArray);
        }
    }
    
    public function load($filename = null, $inArray = false)
    {
        if (!$filename) {
            $filename = $this->filename;
        }
        if ($inArray) {
            try {
                $this->content = array();
                foreach((array)file($this->filename) as $number => $string)
                {
                    $this->content[$number] = $string;/*iconv(
                        peProject::getCharsetIn(), 
                        peProject::getCharsetTo(), 
                        $string
                    );*/
                }
                return $this->content;
            } catch(Exception $ex) {
                return null;
            }
        } else {
            try {
                $this->content = file_get_contents($this->filename); /*iconv(
                    peProject::getCharsetIn(), 
                    peProject::getCharsetTo(), 
                    file_get_contents($this->filename)
                );*/
                return $this->content;
            } catch(Exception $ex) {
                return null;
            }
        }
        return null;
    }
    
    public function save($filename = null, $content = null, $flags = 0)
    {
        if (!$filename) {
            $filename = $this->filename;
        } if (!$content) {
            $content = $this->content;
        } if (!$flags) {
            $flags = $this->flags;
        }
        
        $this->getFileName($filename);
        try {
            return file_put_contents($this->filename, $content, $flags);
        } catch(Exception $ex) {
            return null;
        }
        return null;
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function free()
    {
        unset($this->content);
        unset($this->filename);
        unset($this);
    }
    
    protected function getFileName($filename)
    {
        $this->filename = pePath_Root . $filename;
        return $this->filename;
    }
    
    public static function lastTime($filename)
    {
        return filemtime($filename);
    }
}