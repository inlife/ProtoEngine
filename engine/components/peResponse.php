<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peResponse
 *  @Project: Proto Engine 3
 */

class peResponse 
{
    public $data = array();
    
    public function __construct($html = null) 
    {
        if (isset($html)) {
            $this->html = $html;
        }
    }
    
    public function __set($key, $value = null)
    {
       return $this->data[strtolower($key)] = $value;
    }

    public function __get($key)
    {
        $key = strtolower($key);
        if (!array_key_exists($key, $this->data)) {
            $this->data[$key] = new self();
        } 
        return $this->data[$key];
    }
    
    public function __toString() 
    {
        return "";
    }
    
    public function isEmpty()
    {
        $result = true;
        foreach($this->data as $element)
            if (!empty($element)) $result = false;
        return $result;
    }
}