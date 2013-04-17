<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTpl_block
 *  @Project: Proto Engine 3
 */

class peTpl_block
{
    
    public static function plugin()
    {
        peStorage::add(peTpl_Plugins, __CLASS__, true);
    }
    
    public static $openedBlocks = array();
    public static $closedBlocks = array();
    public static $syntax = array(
        "block\s{0,5}\(\s{0,5}(\S{1,64})\s{0,5}\)",
        "endblock"
    );
    
    public $line;
    public $content = array();
    
    public function __construct($line) 
    {
        $this->line = $line;
    }
    
    public function fill(&$tpl, $line)
    {
        for($i = $this->line + 1; $i < $line; $i++) {
            if (isset($tpl[$i]) && !empty($tpl[$i])) {
                $this->content[] = $tpl[$i];
                $tpl[$i] = null;
            }
        }
        $tpl[$line] = preg_replace(peTemplate::exp(self::$syntax[1]), null, $tpl[$line]);
        $this->content = implode(peTpl_Imploder, (array)$this->content);
    }
    
    public static function syntax(&$tpl, &$n)
    {
        $matches = array();
        if (preg_match(peTemplate::exp(self::$syntax[0]), $tpl[$n], $matches)) {
            self::$openedBlocks[$matches[1]] = new self($n);
        }
        if (preg_match(peTemplate::exp(self::$syntax[1]), $tpl[$n])) {
            $block = end(self::$openedBlocks);
            $name = key(self::$openedBlocks);
            if (isset($block) && isset($name)) {
                unset(self::$openedBlocks[$name]);
                $block->fill($tpl, $n);
                self::$closedBlocks[$name] = $block;
            }
        }
    }
    
    public static function replace(&$tpl)
    {
        foreach(self::$closedBlocks as $name => $block) 
        {
            $block_name = peTemplate::exp(str_replace("(\S{1,64})", $name, self::$syntax[0]));
            $tpl = preg_replace($block_name, $block->content, $tpl, 1);
            $tpl = preg_replace($block_name, null, $tpl);
        }
    }
}