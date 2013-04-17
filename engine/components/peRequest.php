<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peRequest
 *  @Project: Proto Engine 3
 */

class peRequest
{
    protected $_data = array();
    protected static $types = array(
        "b" => peInput_Bool,
        "i" => peInput_Int,
        "t" => peInput_Text,
        "h" => peInput_Html,
        "s" => peInput_Str
    );
    
    public function __construct() 
    {
        $local_request = array_change_key_case($_REQUEST, CASE_LOWER);
        foreach((array)func_get_args() as $param)
        {
            $limit = 255;
            @list($string, $type) = explode(":", $param);
            if (!isset($type)) { 
                $type = "s"; 
            } else if (intval($type) > 0) {
                $limit = intval($type);
                $type = "s";
            }
            
            $format = self::$types[$type];
            
            if (isset($string)) {
                $this->$string = self::getInput(@$local_request[strtolower($string)], $format, $limit);
            }
        }
    }
    
    public function extract()
    {
        return $this->_data;
    }
    
    public function __set($Key, $Value)
    {
	return $this->_data[strtolower($Key)] = $Value;
    }

    public function __get($Key)
    {
        if (@$result = $this->_data[strtolower($Key)]) {
            return $result;
        } else {
            return null;
        }
    }
    
    static protected function getInput($input, $format = peInput_Str, $limit = 255)
    {
        if ($format & peIO_Trim)    $input = trim($input);
        if ($format & peIO_NltoBr)  $input = nl2br($input);
        if ($format & peIO_Spec)    $input = htmlspecialchars($input);
        if ($format & peIO_Slash)   $input = addslashes($input);
        if ($format & peIO_Substr)  $input = substr($input, 0, $limit);
        if ($format & peIO_Int)     $input = intval($input);
        if (empty($input))          return null;
        if ($format & peIO_Bool)    $input = settype($input, "bool");
        return $input;
    }
}