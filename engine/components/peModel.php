<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peModel
 *  @Project: Proto Engine 3
 */

abstract class peModel extends peHttp
{
    protected $_data = array();
    protected $_queue = array();

    public function copy($params)
    {
        $this->_data = array_replace($this->_data, $params->extract());
    }
    
    public function __get($name) 
    {
        if ($result = @$this->_data[strtolower($name)]) {
            return $result;
        } else {
            return null;
        }
    }
    
    public function __set($name, $value) 
    {
        if (@$this->_data[strtolower($name)] = $value) {
            return true;
        } else {
            return false;
        }
    }   

    public function call($name) 
    {
        array_push($this->_queue, $name);
        return $this;
    }
    
    public function _recall()
    {
        $name = "callable_" . array_shift($this->_queue);
        if (is_callable(array($this, $name))) {
            return $this->$name();
        }
        return null;
    }
    
    public function _getdata()
    {
        return $this->_data;
    }
    
    public function query()
    {
        return new peQuery;
    }
}