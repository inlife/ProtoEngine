<?php

/**
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTemplate
 *  @Project: Proto Engine 3
 */

abstract class peTemplate
{
    public static $blocked = 0;
    public static $blockName = null;
    protected static $data;
    protected static $cacheMap = array();
    
    public static function main($data)
    {
        self::$data = $data;
        $template = self::replace(self::call(self::$data->html, true), true);
        
        /* Hook */
        peHook::onPagePrint($data, $template);
        
        print($template);
        if (peProject::getDebug()) {
            peCore::debugPanel();
        }
    }
    
    public static function call($name, $base = false) {
        return self::assemble(
            self::handle(
                self::load($name, $base), 0, $name
            )
        );
    }
    
    public static function exp($string)
    {
        return peTpl_ExpressionL . $string . peTpl_ExpressionR;
    }
    
    public static function checkMap($line, $name) 
    {
        if (!empty($name) && isset(self::$cacheMap[$name])) {
            if (in_array($line, self::$cacheMap[$name])) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }
    
    public static function handle($tpl, $n = 0, $cname = null)
    {
        for(;$n <= count($tpl); $n++)
        {
            if (isset($tpl[$n]) && !empty($tpl[$n]) && self::checkMap($n, $cname)) {
                if ($n == 0 && strpos($tpl[$n], peTpl_CacheMap) !== false) {
                    $map = explode(peTpl_CacheMapSym, $tpl[$n]);
                    array_shift($map);
                    $name = array_shift($map);
                    self::$cacheMap[$name] = $map;
                    $tpl[$n] = null;
                    continue;   
                }
                if (strpos($tpl[$n], peTpl_SyntaxL) !== false || strpos($tpl[$n], peTpl_SyntaxR) !== false) {
                    self::process($tpl, $n); 
                }
                if (peTpl_Compression) {
                    $tpl[$n] = trim($tpl[$n]);
                }
            }
        }
        return $tpl;
    }
    
    public static function replace($tpl, $base = false)
    {
        foreach((array)peStorage::get(peTpl_Plugins) as $plugin)
        {
            if (is_callable($plugin . "::replace")) {
                $plugin::replace($tpl, self::$data);
            }
        }
        
        if (!$base || !peTemplate::isCaching()) {
            $_host  = peProject::getHost();
            $_theme = peProject::getSiteTheme();

            $tpl = preg_replace(
                '#(href|src|action)="([^\{:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#', 
                '$1="'. $_host .'$2$3', $tpl
            );
            foreach(peProject::getTplDirs() as $value) {
                $tpl = str_replace(
                    $_host . $value, $_host . peDir_Tpl . us . $_theme . us . $value, $tpl
                );
            }
        }
        return $tpl;
    }
    
    private static function process(&$tpl, &$n)
    {
        foreach((array)peStorage::get(peTpl_Plugins) as $plugin)
        {
            if (!self::$blockName || (self::$blockName == $plugin)) {
                if (isset($tpl[$n]) && !empty($tpl[$n])) {
                    if (is_callable($plugin . "::syntax")) {
                        $plugin::syntax($tpl, $n, self::$data);
                    }
                }
            }
        }
    }

    private static function load($name, $base = false)
    {
        $tplFile = new String(peDir_Tpl . ds . peProject::getSiteTheme());
        $tplFile->concat(ds . $name . peTpl_Extension);
        
        if ($base && peTpl_Cache && self::$data->cache) {
            $cacheFile = new String(peDir_Tpl . ds . peProject::getSiteTheme());
            $cacheFile->concat(ds . peDir_Cache . ds . $name . peTpl_CacheExt);
            
            $updatetime = @peFile::lastTime($tplFile) > peFile::lastTime($cacheFile);
            $timeout = @(time() - peFile::lastTime($cacheFile)) > peTpl_CacheTime;
            $pTpl = new peFile($cacheFile, true);
            if ($timeout || $pTpl->isEmpty() || $updatetime) {
                return self::cacheFile($tplFile, $pTpl, $name);
            } 
        } else {
            $pTpl = new peFile($tplFile, true);
            if ($pTpl->isEmpty()) peCore::error("peTemplate: File '$name' doesn't exists");
        }
        return $pTpl->getContent();
    }
    
    private static function cacheFile($filename, $pointer, $name)
    {
        $tpl = new peFile($filename, true);
        if ($tpl->isEmpty()) peCore::error("peTemplate: File '$name' doesn't exists");
        $html = self::replace(
            self::assemble(
                self::handle($tpl->get())
            )
        );
        $content = preg_replace(peTpl_ExpressionL . "/i", peTpl_SyntaxL . " ", $html);
        
        $pointer->set($content);
        $pointer->save();
        
        $template = $pointer->load(null, true);
        $lines = array();
        foreach($template as $n => $line) {
            if (!strpos($line, peTpl_SyntaxL)) {
                if (isset($template[$n + 1]) && !strpos($template[$n + 1], peTpl_SyntaxL)) {
                    $template[$n] = trim($template[$n]);
                }
            }
        }
        $template = explode("\n", implode(peTpl_Imploder, $template));
        foreach($template as $n => $line) {
            if (strpos($line, peTpl_SyntaxL)) {
                $lines[] = $n + 1;
            }
            $template[$n] = trim($template[$n]);
            if (!empty($template[$n])) {
                $template[$n] .= "\n";
            }
        }
        array_unshift($lines, $name);
        array_unshift($lines, peTpl_CacheMap);
        
        $first = implode(peTpl_CacheMapSym, $lines) . "\n";
        array_unshift($template, $first);
        
        $pointer->set(self::assemble($template));
        $pointer->save();
        
        return $template;
    }
    
    private static function assemble($tpl)
    {
        return implode(peTpl_Imploder, $tpl);
    }
    
    public static function isCaching()
    {
        return self::$data->cache;
    }
}