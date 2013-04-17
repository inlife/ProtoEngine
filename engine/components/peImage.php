<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peImage
 *  @Project: Proto Engine 3
 */

class peImage 
{
    public $image;
    public $name;
    public $required_size;
    
    protected static $types = array(
        "image/gif", "image/jpeg", "image/png", "image/pjpeg"
    );
    protected static $extensions = array(
        "jpg", "jpeg", "gif", "png"
    );
    
    public function __construct($name, $size = 20000)
    {
        if (isset($_FILES[$name]) && !empty($_FILES[$name])) {
            $this->image = (object)$_FILES[$name];
            $this->required_size = $size;
            if (!$this->image->error) {
                return true;
            }
        }
    }
    
    public function save($name = null)
    {
        if (!$name) {
            $name = $this->image->name;
        }
        if ($this->checkType() && $this->checkExtension()) {
            if ($this->image->size <= $this->required_size) {
                $path = pePath_Uploads . $name;
                $this->name = $name;
                return move_uploaded_file($this->image->tmp_name, $path);
            } else {
                peHttp::error(7);
            }
        }
    }
    
    public function getUrl()
    {
        return peProject::getHost() . peDir_Uploads . us . $this->name;
    }
    
    public function getPath()
    {
        return peDir_Uploads . $this->name;
    }
    
    protected function checkType()
    {
        return in_array($this->image->type, self::$types);
    }
    
    protected function checkExtension()
    {
        $ext = explode(".", $this->image->name);
        return in_array(end($ext), self::$extensions);
    }
}