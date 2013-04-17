<?php
/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: peCore
 *  @Project: Proto Engine 3
 */

function peRequire($filename)
{
    return include_once($filename);
}

function pre($data)
{
    if (peProject::getDebug())
    {
        print("<pre>");
        print_r($data);
        print("</pre>");
    }
}

abstract class peCore
{    
    protected static $time;
    protected static $memory;

    public static function init($params)
    {
        /* Debugging */
        self::$time = self::getTime();
        self::$memory = memory_get_usage();
        
        /* Loading constants and loader */
        peRequire("constants.php");
        peRequire(pePath_Components . "peLoader.php" );
        
        /* Import main components */
        peLoader::import("components.peString");
        peLoader::import("components.peHooks");
        peLoader::import("components.peProject");
        peLoader::import("components.peRequest");
        
        /* Loading project settings */
        peProject::load($params);
        
        /* Import project components */
        foreach(peProject::getComponents() as $component) { peLoader::import($component); }
        
        /* Loading all plugins of engine */
        self::plugins();
        
        /* Hooks */
        peHook::onCoreInit();
        
        /* Sending headers */
        self::sendHeaders();
        
    }
    
    public static function error($text, $file = null, $line = 0)
    {
        if (peProject::getDebug()) {
            pre(sprintf("[ERROR](%s on line: %n):%s", $file, $line, $text));
            die();
        }
    }
    
    public static function debug()
    {
        $data = array(
            round(self::getTime() - self::$time, 4),
            (memory_get_usage() - self::$memory) / 1000,
            peSys_Version
        );
	   return vsprintf("Gen.time: %s Sec, Used.mem: %s KB, Ver: %s", $data);
    } 

    private static function getTime()
    {
        list($usec, $seconds) = explode(" ", microtime());
        return ((float)$usec + (float)$seconds);
    }
    
    private static function plugins()
    {
        if (peSys_PluginsEnabled) {
            self::loadPlugins(pePath_Plugins);
        }
    }

    private static function sendHeaders()
    {
        if (!peProject::getDebug()) {
            error_reporting(0);
        }
        header("Content-type: text/html; charset=" . peProject::getCharsetTo());
    }
    
    private static function loadPlugins($path)  
    {
        $files = scandir($path);
        foreach($files as $file) 
        {
            if ($file != "." && $file != "..") {
                $filename = $path . $file;
                if (is_file($filename) && strpos($file, ".php")) {
                    $_classesB = get_declared_classes();
                    peRequire($filename);
                    $_classesA = array_diff(get_declared_classes(), $_classesB);
                    if (count($_classesA) > 0) {
                        $class = current($_classesA);
                        if (is_callable($class . "::plugin")) {
                            $class::plugin();
                        } 
                    }
                } elseif (is_dir($filename)) {
                    $_func = __FUNCTION__;
                    self::$_func($filename);
                }        
            }       
        }    
    }   
}