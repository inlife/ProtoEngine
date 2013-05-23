<?php
/*
 *  @Author: Vladislav Gritsenko (Inlife)
 *  @Name: settings
 *  @Project: Proto Engine 3
 */
if (!defined('__DIR__'))  define('__DIR__', dirname(__FILE__));    

define("peSys_Version", "3.2.4");

// Dirs & Paths
define("ds", DIRECTORY_SEPARATOR);
define("us", "/");

define("peDir_Engine", "engine");
define("peDir_Plugins", "plugins");
define("peDir_Components", "components");
define("peDir_Uploads", "uploads");
define("peDir_Tpl", "tpl");
define("peDir_Cache", "cache");

define("pePath_Root", __DIR__ . ds);
define("pePath_Engine", pePath_Root . peDir_Engine . ds);
define("pePath_Plugins", pePath_Engine . peDir_Plugins . ds);
define("pePath_Components", pePath_Engine . peDir_Components . ds);
define("pePath_Uploads", pePath_Root . peDir_Uploads . ds);
define("pePath_Tpl", pePath_Root . peDir_Tpl . ds);

// IO
define("peIO_Trim", 1);
define("peIO_Spec", 2);
define("peIO_Slash", 4);
define("peIO_Substr", 8);
define("peIO_Int", 16);
define("peIO_Bool", 32);
define("peIO_NltoBr", 64);

define("peInput_Html", peIO_Slash   | peIO_NltoBr | peIO_Trim);
define("peInput_Text", peInput_Html | peIO_Spec);
define("peInput_Str",  peInput_Text | peIO_Substr);
define("peInput_Int",  peInput_Str  | peIO_Int);
define("peInput_Bool", peInput_Str  | peIO_Bool);

// System constants
define("peSys_IncludeFileExt", ".php");
define("peSys_SecCode", "32whs64");
define("peSys_SessionEnabled", true);
define("peSys_PluginsEnabled", true);

// Cookies
define('peCookie_Expire', time() + 60 * 60 * 24 * 30);

// Other
define("NL", "\n");
define("RL", "\r");
define("_BR_", RL . NL);

// Template
define("peClass_Prefix", "pe");
define("peTpl_ControllerPostfix", "Controller");
define("peTpl_ActionPostfix", "Action");
define("peTpl_DefaultAction", "index");
define("peTpl_DefaultPage", "main");
define("peTpl_Plugins", "tpl_plugins");
define("peTpl_SyntaxL", "{%");
define("peTpl_SyntaxR", "%}");
define("peTpl_ExpressionL", "/\{\%\s{0,5}\@{0,1}\s{0,5}");
define("peTpl_ExpressionR", "\s{0,5}\%\}/i");
define("peTpl_Imploder", null);
define("peTpl_Extension", ".html");
define("peTpl_CacheExt", ".chtm");
define("peTpl_CacheTime", 3600);
define("peTpl_Cache", true);
define("peTpl_CacheMap", "!peCache_Map_File!");
define("peTpl_CacheMapSym", ":");
define("peTpl_NoCacheSym", "@"); // no-cache symbol also in peTpl_ExpressionL
define("peTpl_Compression", !peTpl_Cache);

/* Template standarts:
 * 
 * page.title
 * url(main::index)
 * extends(name)
 * block(name) endblock
 * if(user.logined) else endif
 * foreach(articles::article) endfor
 * ! - for no-caching
 */