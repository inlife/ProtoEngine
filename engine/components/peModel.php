<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peModel
 *  @Project: Proto Engine 3
 */

abstract class peModel
{
    protected $_data;

    public function copy($params)
    {
        $this->_data = array_replace($this->_data, array_intersect_key($params->extract(), $this->_data));
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

    public function query()
    {
        return new peQuery;
    }
}