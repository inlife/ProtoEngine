<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peQuery
 *  @Project: Proto Engine 3
 */

class peQuery 
{ 
    private static $_pointer = null;
    private static $_requestFrom = array("select", "delete");
    private static $_requestSet = array("insert", "update");
    protected $_method = null;
    protected $_table = null;
    protected $_info = null;
    protected $_condition = null;
    protected $_order = null;
    protected $_limit = null;

    public function __call($name, $args)
    {
        $name = strtolower($name);
        if (in_array($name, self::$_requestFrom) || in_array($name, self::$_requestSet)) {
            $this->_method = $name;
            if (!empty($args[0])) {
                $this->_info = $args[0];
            }
            return $this;
        }
        switch($name) 
        {
            case "table": $this->_table = $args[0];     break;
            case "values":
            case "set":   $this->_info = $args[0];      break;
            case "where": $this->_condition = $args[0]; break;
            case "order": $this->_order = $args;        break;
            case "limit": $this->_limit = $args;        break;
            case "run":   return $this->query(@$args[0]);break;
            case "clear":
                $this->_method = null;
                $this->_condition = null;
                $this->_info = null;
                $this->_table = null;
            break;
        }
        return $this;
    }
    
    private function query($return = false)
    {
        /* Adding method*/
        if (empty($this->_method)) {
            peCore::error("No method", "modelprovider", __LINE__);
        }
        $query = new String($this->_method);
        $query->toUpper();
        
        /* Adding method postfixes */
        if (in_array($this->_method, self::$_requestFrom)) {
            if ($this->_method == self::$_requestFrom[0]) {
                if (!empty($this->_info)) {
                    $query->space()->concat($this->_info);
                } else {
                    $query->space()->concat("*");
                }
            }
            $query->space()->concat("FROM");
        } elseif ($this->_method == self::$_requestSet[0]) {
            $query->space()->concat("INTO");
        }
        
        /* Adding table */
        if (empty($this->_table)) {
            peCore::error("No table", "modelprovider", __LINE__);
        } else {
            $query->space()->concat($this->_table);
        }
        
        /* Adding data */
        if (!empty($this->_info) && in_array($this->_method, self::$_requestSet)) {
            if ($this->_method == self::$_requestSet[0]) {
                $query->space()->concat(self::listKeysValues($this->_info));
            } elseif ($this->_method == self::$_requestSet[1]) {
                $query->concat(" SET ")->concat(self::assignKeysValues($this->_info));
            }
        }
        
        /* Adding condition */
        if (!empty($this->_condition) && $this->_method != self::$_requestSet[0]) {
            $query->concat(" WHERE ")->concat(self::listValues($this->_condition)); 
        }
        
        /* Adding ordering */
        if (!empty($this->_order) && $this->_method == self::$_requestFrom[0]) {
            $query->concat(" ORDER BY ")->concat($this->_order[0]);
            if (@$this->_order[1] === true) 
            {
                $query->space()->concat("DESC");
            }
        }
        
        /* Adding limitation */
        if (!empty($this->_limit) && $this->_method == self::$_requestFrom[0]) {
            $query->space()->concat("LIMIT");
            $query->space()->concat($this->_limit[0]);
            if (count($this->_limit) > 1) {
                $query->concat(",")->concat($this->_limit[1]);
            }
        }
        
        /* Running query */
        $result = self::getPointer()->query($query);
        if (self::getPointer()->errno) {
            $p = self::getPointer();
            peCore::error("Mysqli: query error (" . $p->errno . "), " . $p->error);
        } else { 
            if (!empty($result) && is_object($result))
            {
                $objects = array();
                while($obj = $result->fetch_object())
                {
                    $objects[] = $obj;
                }
                if (count($objects) <= 1) {
                    return @$objects[0];
                } else {
                    return $objects;
                }
            }
        }
        return null;
    }
    
    private static function escapeString($value)
    {
        if(@gettype($value) == "string" || $value === null) {
            return "'".$value."'";
        } else {
            return $value;
        }
    }
    
    private static function listValues($array, $mod = "AND")
    {
        foreach($array as $key => $value)
        {
            if (gettype($key) != "integer") {
                $value = self::escapeString($value);
                $data[] = $key . "=" . $value;
            } else {
                $data[] = $value;
            }
        }
        return implode(' '.$mod.' ', $data);
    }
    
    private static function listKeysValues($params)
    {
        foreach($params as $value)
        {
            $value = self::escapeString($value);
            $data[] = $value;
        }
        return '(' .implode(',', array_keys($params)). ') VALUES (' .implode(',', $data). ')';
    }
    
    private static function assignKeysValues($params)
    {
        foreach($params as $value => $value)
        {
            $value = self::escapeString($value);
            $data[] = $value .'='. $value;
        }
        return implode(',', $data);
    }
    
    protected static function getPointer()
    {
        if (!self::$_pointer) {
            self::$_pointer = new mysqli(
                peProject::getMysqlHost(), 
                peProject::getMysqlUser(), 
                peProject::getMysqlPass(), 
                peProject::getMysqlName() 
            );
            self::$_pointer->query("SET NAMES utf8");   
            if (self::$_pointer->connect_error) {
                peCore::error('Connect Error (' . self::$_pointer->connect_errno . ') ' . self::$_pointer->connect_error);
            }
        }
        return self::$_pointer;
    }
}