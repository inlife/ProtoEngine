<?php

/**
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peTemplate
 *  @Project: Proto Engine 3
 */

abstract class peTemplate
{
    protected static $data;
    protected static $forceCache = false;
    
    public static function main($data)
    {
        self::$data = $data;
        $template = self::replace(self::call(self::$data->html));
        
        /* Hook */
        peHook::onPagePrint($data, $template);
        
        print(
            str_replace(
                "{% page.debug %}", 
                peCore::debug(), 
                $template
            )
        );
    }
    
    public static function call($name) {
        return self::assemble(
            self::handle(
                self::load($name)
            )
        );
    }
    
    public static function exp($string)
    {
        return peTpl_ExpressionL . $string . peTpl_ExpressionR;
    }
    
    public static function handle($tpl, $n = 0)
    {
        for(;$n <= count($tpl); $n++)
        {
            if (isset($tpl[$n]) && !empty($tpl[$n])) {
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
    
    public static function replace($tpl)
    {
        foreach((array)peStorage::get(peTpl_Plugins) as $plugin)
        {
            if (is_callable($plugin . "::replace")) {
                $plugin::replace($tpl, self::$data);
            }
        }
        
        $_host  = peProject::getHost();
        $_theme = peProject::getSiteTheme();
        
        $tpl = preg_replace(
                '#(href|src|action)="([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#', 
                '$1="'. $_host .'$2$3', $tpl
        );
        foreach(peProject::getTplDirs() as $value) {
            $tpl = str_replace(
                $_host . $value, $_host . peDir_Tpl . us . $_theme . us . $value, $tpl
            );
        }
        return $tpl;
    }
    
    private static function process(&$tpl, &$n)
    {
        foreach((array)peStorage::get(peTpl_Plugins) as $plugin)
        {
            if (isset($tpl[$n]) && !empty($tpl[$n])) {
                if (is_callable($plugin . "::syntax")) {
                    $plugin::syntax($tpl, $n, self::$data);
                }
            }
        }
    }

    private static function load($name)
    {
        $tplFile = new String(peDir_Tpl);
        $tplFile->concat(ds)->concat(peProject::getSiteTheme())
                ->concat(ds)->concat($name)
                ->concat(peTpl_Extenstion);
        if (peTpl_Cache) {
            $cacheFile = new String(peDir_Tpl);
            $cacheFile->concat(ds)->concat(peProject::getSiteTheme())
                    ->concat(ds)->concat(peDir_Cache)
                    ->concat(ds)->concat($name)
                    ->concat(peTpl_CacheExt);

            if (!file_exists($cacheFile) || peFile::lastTime($tplFile) > peFile::lastTime($cacheFile)) {
                $pTpl = new peFile($tplFile, true);
                self::cacheFile($pTpl, $cacheFile);
            } else {
                $pTpl = new peFile($cacheFile, true);
            }
        } else {
            $pTpl = new peFile($tplFile, true);
        }
        return $pTpl->getContent();
    }
    
    private static function cacheFile($pointer, $name)
    {
        $tpl = $pointer->content;
        for($n = 0;$n <= count($tpl); $n++)
        {
            if (isset($tpl[$n]) && !empty($tpl[$n])) {
                $tpl[$n] = trim($tpl[$n]);
            }
        }
        $exp = explode(peTpl_SyntaxL, implode(peTpl_Imploder, $tpl));
        $template = array();
        foreach($exp as $line => $string)
        {
            if ($line > 0) {
                $exp2 = explode(peTpl_SyntaxR, $string);
                $template[] = (peTpl_SyntaxL . $exp2[0] . peTpl_SyntaxR);
                if (trim($exp2[1])) $template[] = ($exp2[1]);
            } else {
                $template[] = trim($string);
            }
        }
        $pointer->content = $template;
        $content = implode("\n", $template);
        $pointer->save($name, $content);
    }
    
    private static function assemble($tpl)
    {
        return implode(peTpl_Imploder, $tpl);
    }
}