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
        if (!$filename) $filename = $this->filename;
        
        if ($inArray) $this->content = @file($this->filename);
        else $this->content = @file_get_contents($this->filename);
        
        return ($this->isEmpty()) ? null : $this->content;
    }
    
    public function save($filename = null, $content = null, $flags = 0)
    {
        if (!$filename) $filename = $this->filename;
        if (!$content)  $content = $this->content;
        if (!$flags)    $flags = $this->flags;
        
        $this->getFileName($filename);
        try {
            return file_put_contents($this->filename, $content, $flags);
        } catch(Exception $ex) {
            return null;
        }
        return null;
    }
    
    public function set($data) 
    {
        return $this->content = $data;
    }
    
    public function get()
    {
        return $this->content;
    }
    
    public function getContent()
    {
        return $this->get();
    }
    
    public function free()
    {
        unset($this->content);
        unset($this->filename);
        unset($this);
    }
    
    protected function getFileName($filename)
    {
        if (strpos($filename, pePath_Root) === false) {
            $this->filename = pePath_Root . $filename;
        }
        return $this->filename;
    }
    
    public function isEmpty()
    {
        if (is_array($this->content)) {
            return (count($this->content) > 1 || !empty($this->content[0])) ? false : true;
        } else {
            return empty($this->content);
        }
    }
    
    public static function lastTime($filename)
    {
        return @filemtime($filename);
    }
}