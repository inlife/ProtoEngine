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
        self::$time = microtime(true);
        self::$memory = memory_get_usage(true);
        
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
    
    public static function debugPanel()
    {
        $debug = '<script>function pedbgh(e){e.style.display="none"}</script><style type="text/css">#pe-debug-panel{z-index:999999;position:fixed;bottom:0;left:0;height:38px;width:100%%;background-color:#f7f7f7;color:#2f2f2f;font:11px Verdana,Arial,sans-serif;text-align:left;background-image:-moz-linear-gradient(top,#d0d0d0,#ffffff);background-image:-webkit-gradient(linear,0%% 0,0%% 100%%,from(#d0d0d0),to(#ffffff));background-image:-o-linear-gradient(top,#d0d0d0,#ffffff);background:linear-gradient(top,#d0d0d0,#ffffff);border-top:1px solid #d0d0d0}#pe-debug-panel .element{white-space:nowrap;color:#2f2f2f;display:block;min-height:38px;border-right:1px solid #d0d0d0;padding:0;float:left;cursor:default}#pe-debug-panel .element .sub{display:block;text-decoration:none;margin:0;padding:12px 8px;min-width:20px;text-align:center;vertical-align:middle}#pe-debug-panel .element .sub img{padding:0;margin-right:5px;border:0;position:relative;top:-9px;float:left}</style><div title="Click to close" onclick="pedbgh(this)" id="pe-debug-panel"><div class="element"><div class="sub"><img alt="Proto Engine" title="Proto Engine" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAadEVYdFNvZnR3YXJlAFBhaW50Lk5FVCB2My41LjEwMPRyoQAAAjNJREFUWEe9V6GOwkAURCKRyJPIk+eoPEVOgiPBIBA4kKBOESQ4BAIJP0CQF/4AhyFBkmCQvZn2lW6323aBlkkmgd333kzfbttt6RnU6/Ua6Gj8kuliAIGm4zgL8Ay6CbyBK8S2wYqkvgYRPioitrwgtw+WpdRjQOIHivxpRZ/hGbU+pawdkIA856IUeZU31GxK+XSIuKnIy0TttsiYgQC2Pc8r18lOOCIXBwLyWPMscnNWRTIEBruG4KI4FVkfEC9jMO3+zptcirAL+NM2BBXNX5H31n6lTjYaDXe/37tJOJ1O7nw+j8Qvl0v3cDhIRBQcHwwG93jh0ROX9kd2/mQykdR0BCbSzKro9Xp3DRLaNRr4VAdJFrbB9Xr14nXQEMlOqVC7RkK7SwN81kcmdANB+1qtVqzoeDyWXz64FEEdxqugqWBOODVuQN2A2jq93bbdIjabTUQHXNBA7P4vwgCXy7ARPQPWHTAtQafTkV8+ttttpFYGZzTwrU/YXhXNMN4WhrugTwMVdZC0NcA4xuvLkgSDAf/FhD87dSLLgOlBxNZnQTPAl5J/WsKPoTIRM6A7z4kLT5yAgSoGeKD0Jt9hAJo/Iu8Dg9Ng8g0G1iIbQu9CkYSW+ZDKtpgS8iQ0hiJnBgNMiTlxJjLpgImRIflVhrveBjDBt2QueyKz7UlAIo/pa73gA9yhRvIx3BYowkMLP0xtvhnYtTVyovd5XuAVgdwjNMRHOMkv4hFFwQc+Rkulf0Mu9Xl11abnAAAAAElFTkSuQmCC" />Proto Engine %s</div></div><div class="element"><div class="sub"><img style="top:-6px" alt="Time" title="Time" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAEDUlEQVR4XqWWf0xbVRTH92h51Gxjilm2BMSo4B/7Y864mJjoQohL1ApExNSQtIPBYCioK/Io/QktLVAKm5StFMbchmz/LGLVxTgTDdPIxC0s6pgkJkQMSpgUp9XC69rn9ya3zbXSFvQkn3yTk/POuT339J3HSZK0KZXZnb1ZkCfBTpANFkAYTBgE7bfQlCZPFNDe1cNDKkAZ2J8kbh4yBvqNzY0zieLW/EW2TlchxAvywHotDDxAMOneDKYsZHV0vw7pAbIEya6D7SA3QcFJUGrWN80nLNRmdzohTYC1H8BbYMxiEOaZWBltqQqo4w5G4p5C/Oy/CllsnTW0XVETQQvP832GZm04ScskPLsLehY8xvinwd42ky4YGwaz1ZEHOcYEBcDzVrP+MjSNktCQ7HtbR/e+UCh0Ho5i6t5Fr+CVWOuMrfZROMqZBCXtrYYPmXasB8nqcPKiGLrM/LIwyEeuWbnebMulfY7aOYfVRIqkg5Tm9gw98kcg4FIoMtAAYRz5KuGeAjKKFjTII1JESRzMCfSAT9ouSt9x76PLy799gFvO5OXpCnI4HHJaZ2o7RweEWDFo4Jr0ZnJ6JXV+4bS3FUDviiY71j+we3Fx0ZeRoejEFMWGxXXU/bh/eXkMnVds2by5yqBrfC96WMFgKYQib8x2y6VIZA/j+ASks3fzcN6DP83Nza0Eg0GXwWyV263mAXuXa9/Skv88icu65241inzMPCPL4PnPVlZWwoxvD6cV9Ow/VtPTZR+jrYvZ8OmRnKvXpi5icHKQ5IwYCqmIf+eOHS9bjM2fr9HV243Nhh+h2dTRIo9EImxAEMji76eqQv3zJkkq+vLKpG91dfUAx3G/3597XzmKfJXgLjnkFVkHCkkB6BbqyAXSWmNdVan5RRRF1dfXpmz5eQ85WwTtN0nGH3WkbMYR4OrfaCKjGL2nEfdRZx10G/ivFm44ItwLvcH4niWtm2AKKS+86wuVlZak/Y9CfyFnMVsYTHKH6488A8dHTGClx93rg2aCjVrEfcJ7a/rmzAzzdh9HvgIyDGSkZ8ED0V2mM7b6Ottb7zDTt16WvrtxszFuhQzG1sShutfK4RhlEnyqeqlU+XRhwfYNFPsVeZ6E+pghuQ72Dnn6wrE1UV1bfyluZY9nbt36Yq+rI40OB5egwCq4hedV0BPMW0UEBSe9/RP/2EcHa14lya7Gre8FYCwpeu6dkiKlAg6CDIQpf+K5bKgTvABYqz41eHx4zQ1bUV1H7ukSW4zip6+nCRCkPhK7n1kJLMLpk57upN8MB6oOb6P3pQQbtdtAc2Z44P3UX0EUzcHaYkgH3ZSpTKTTZTl7yutf9+cWi7qy5gmIkrYpi7Y1AGbAPLgALo68PehPludv46qVrL8kttQAAAAASUVORK5CYII=" />%d ms</div></div><div class="element"><div class="sub"><img title="Used Memory" alt="Used Memory" src="data:image/png;base64,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" />%d kb</div></div><div class="element"><div class="sub"><img alt="The number of database requests" title="The number of database requests" src="data:image/png;base64,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" />%d</div></div></div>';
        $data = array(
            peSys_Version,
            round((microtime(true) - self::$time)  * 1000),
            round((memory_get_usage(true) - self::$memory) / 1000),
            peQuery::getRequests()
        );
        print(vsprintf($debug, $data));
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
        header("Content-type: text/html; charset=" . peProject::getCharset());
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