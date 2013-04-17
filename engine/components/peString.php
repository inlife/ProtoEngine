<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: String
 *  @Project: Proto Engine 3
 */

class String 
{
    protected $_string = '';
    
    public function __construct($string = '')
    {
        $this->_string = (string)$string;
    }
    
    public function concat($string)
    {
        $this->_string .= (string)$string;
        return $this;
    }
    
    public function concatStart($string)
    {
        $this->_string = (string)$string . $this->_string;
        return $this;
    }
    
    public function space($size = 1)
    {
        while($size > 0)
        {
            $this->concat(" ");
            $size--;
        }
        return $this;
    }
    
    public function spaceStart($size = 1)
    {
        while($size > 0)
        {
            $this->concatStart(" ");
            $size--;
        }
        return $this;
    }
    
    public function toUpper()
    {
        $this->_string = strtoupper($this->_string);
        return $this;
    }
    
    public function toLower()
    {
        $this->_string = strtolower($this->_string);
        return $this;
    }
    
    public function length() 
    {
        return strlen($this->_string);
    }
    
    public function size()
    {
        return $this->length();
    }
    
    public function len()
    {
        return $this->length();
    }
    
    public function equals($string)
    {
        if ($this->_string === (string)$string) {
            return true;
        } else {
            return false;
        }
    }
    
    public function __toString() 
    {
        return $this->_string;
    }
}