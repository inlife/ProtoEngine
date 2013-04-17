<?php

/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTpl_condition
 *  @Project: Proto Engine 3
 */

class peTpl_condition {
    
    public static function plugin()
    {
        peStorage::add(peTpl_Plugins, __CLASS__, true);
    }
    
    public static $openedBlocks = array();
    public static $closedBlocks = array();
    public static $syntax = array(
        "if\s{0,5}\(\s{0,5}(\S{1,64})\s{0,5}\)",
        "else",
        "endif"
    );
    
    public $line;
    public $code;
    public $content = array();
    
    public function __construct($line, $code) 
    {
        $this->line = $line;
        $this->code = $code;
    }
    
    public static function addIgnore($string)
    {
        self::$syntax[$string] = $string;
    }
    
    public static function delIgnore($string)
    {
        unset(self::$syntax[$string]);
    }
    
    public function fill(&$tpl, $line, $index = 1, $type = 1)
    {
        $tpl[$this->line] = preg_replace(
                peTemplate::exp(str_replace("(\S{1,64})", $this->code, self::$syntax[0])),
                peTpl_SyntaxL . " if(" . $this->code . " :true:) " . peTpl_SyntaxR,
                $tpl[$this->line]
        );
        for($i = $this->line + 1; $i < $line; $i++) {
            $this->content[$index][] = $tpl[$i];
            $tpl[$i] = null;
        }
        $tpl[$line] = preg_replace(peTemplate::exp(self::$syntax[$type]), null, $tpl[$line]);
    }
    
    public function get(&$data)
    {
        if (class_exists("peTpl_variable")) {
            $pTpl = array(peTpl_SyntaxL . $this->code . peTpl_SyntaxR);
            $result = peTpl_variable::syntax($pTpl, 0, $data, true);
            if (isset($result) && !empty($result)) {
                if ($result) {
                    if (is_array($result)) {
                        if (count($result) > 0) {
                            if (@!$result[0]->isEmpty()) {
                                if (isset($this->content[1]) && count($this->content[1])) {
                                    return $this->content[1];
                                }
                            }
                        }
                    } else {
                        if (isset($this->content[1]) && count($this->content[1])) {
                            return $this->content[1];
                        }
                    }
                }
            }
            if (isset($this->content[2]) && count($this->content[2])) {
                return $this->content[2];
            }
        }
    }
    
    public static function syntax(&$tpl, &$n)
    {
        $matches = array();
        if (preg_match(peTemplate::exp(self::$syntax[0]), $tpl[$n], $matches)) {
            list($first) = explode(".", $matches[1]);
            if (!in_array($first, self::$syntax)) {
                self::$openedBlocks[] = new self($n, $matches[1]);
            } 
            if (class_exists("peTpl_variable")) {
                peTpl_variable::setBlock(true);
            }
        }
        if (preg_match(peTemplate::exp(self::$syntax[1]), $tpl[$n])) {
            $block = end(self::$openedBlocks);
            $key = key(self::$openedBlocks);
            if (isset($block) && isset($key)) {
                $block->fill($tpl, $n, 1, 1);
                self::$openedBlocks[$key] = $block;
            }
        }
        if (preg_match(peTemplate::exp(self::$syntax[2]), $tpl[$n])) {
            $block = end(self::$openedBlocks);
            $key = key(self::$openedBlocks);
            if (class_exists("peTpl_variable")) {
                peTpl_variable::setBlock(false);
            }
            if (isset($block) && isset($key)) {
                unset(self::$openedBlocks[$key]);
                $block->fill($tpl, $n, count($block->content) + 1, 2);
                self::$closedBlocks[$key] = $block;
            }
        }
    }
    
    public static function replace(&$tpl, &$data, $spec = false)
    {
        foreach(self::$closedBlocks as $block) 
        {
            $block_name = peTemplate::exp(str_replace("(\S{1,64})", $block->code . " \:true\:", self::$syntax[0]));
            $tpl = preg_replace($block_name, 
                implode(peTpl_Imploder, (array)peTemplate::handle($block->get($data))), $tpl, 1
            );
        }
    }
    
}