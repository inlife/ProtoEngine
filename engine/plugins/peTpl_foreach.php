<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTpl_block
 *  @Project: Proto Engine 3
 */

class peTpl_foreach
{
    
    public static function plugin()
    {
        peStorage::add(peTpl_Plugins, __CLASS__, true);
    }
    
    public static $blocked = 0;
    public static $used = 0;
    public static $openedBlocks = array();
    public static $closedBlocks = array();
    public static $syntax = array(
        "foreach\s{0,5}\(\s{0,5}(\S{1,64})\s{0,5}\)",
        "endfor"
    );
    
    public $line;
    public $name;
    public $content = array();
    
    public function __construct($line, $name) 
    {
        $this->line = $line;
        $this->name = $name;
    }
    
    public function get(&$data)
    {
        if (class_exists("peTpl_variable")) {
            list($array, $variable) = explode("::", $this->name);
            peTpl_variable::delIgnore($variable);
            $result = array();
            $pTpl = array(peTpl_SyntaxL . $array . peTpl_SyntaxR);
            $val = peTpl_variable::syntax($pTpl, 0, $data, true);
            if (isset($val) && !empty($val)) {
                foreach($val as $object)
                {
                    $data->$variable = $object;
                    $block = implode(peTpl_Imploder, peTemplate::handle($this->content));
                    if (class_exists("peTpl_condition")) {
                        peTpl_condition::replace($block, $data, true);
                    }
                    self::replace($block, $data);
                    $tpl = array($block);
                    peTpl_variable::syntax($tpl, 0, $data);
                    $result[] = $tpl[0];
                }
            }
            return implode(peTpl_Imploder, $result);
        }
        return null;
    }
    
    public function fill(&$tpl, $line)
    {
        $tpl[$this->line] = preg_replace(
                peTemplate::exp(str_replace("(\S{1,64})", $this->name, self::$syntax[0])),
                peTpl_SyntaxL . " foreach(" . $this->name . " :true:) " . peTpl_SyntaxR,
                $tpl[$this->line]
        );
        for($i = $this->line + 1; $i < $line; $i++) {
            if (isset($tpl[$i]) && !empty($tpl[$i])) {
                $this->content[] = $tpl[$i];
                $tpl[$i] = null;
            }
        }
        $tpl[$line] = preg_replace(peTemplate::exp(self::$syntax[1]), null, $tpl[$line]);
    }
    
    public static function syntax(&$tpl, &$n, &$data)
    {
        $matches = array();
        if (preg_match(peTemplate::exp(self::$syntax[0]), $tpl[$n], $matches)) {
            if (class_exists("peTpl_variable")) {
                list(,$name) = explode("::", $matches[1]);
                peTpl_variable::addIgnore($name);
            }
            if (class_exists("peTpl_condition")) {
                list(,$name) = explode("::", $matches[1]);
                peTpl_condition::addIgnore($name);
            }
            if (!self::$blocked) {
                if (strpos($matches[0], peTpl_NoCacheSym) && peTemplate::isCaching()) {
                    peTemplate::$blockName = __CLASS__;
                } else {
                    self::$openedBlocks[] = new self($n, $matches[1]);
                }
            } 
            self::$blocked++;
            self::$used++;
        }
        if (preg_match(peTemplate::exp(self::$syntax[1]), $tpl[$n])) {
            if (self::$blocked == 1) {
                peTemplate::$blockName = null;
                $block = end(self::$openedBlocks);
                $key = key(self::$openedBlocks);
                if (isset($block) && isset($key)) {
                    unset(self::$openedBlocks[$key]);
                    $block->fill($tpl, $n);
                    self::$closedBlocks[] = $block;
                    if (class_exists("peTpl_condition")) {
                        list(, $variable) = explode("::", $block->name);
                        peTpl_condition::delIgnore($variable);
                    }
                }
            }
            self::$blocked--;
        }
    }
    
    public static function replace(&$tpl, &$data)
    {   
        if (self::$used > 0) {
            foreach(self::$closedBlocks as $key => $block) 
            {
                unset(self::$closedBlocks[$key]);
                $block_name = peTemplate::exp(str_replace("(\S{1,64})", $block->name . " \:true\:", self::$syntax[0]));
                $tpl = preg_replace($block_name, $block->get($data), $tpl, 1);
            }
        }
    }
}