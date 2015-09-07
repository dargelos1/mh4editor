<?php
error_reporting(~E_ALL);
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

//APIs (Do not modify) Fort42
if(
	file_exists(__DIR__."/../include/config.php") &&
	file_exists(__DIR__."/../include/functions.php") &&
	file_exists(__DIR__."/../include/mysql_control.php") &&
	file_exists(__DIR__."/../include/login_control.php")
){
include(__DIR__."/../include/config.php");
include(__DIR__."/../include/functions.php");
include(__DIR__."/../include/mysql_control.php");
//include(__DIR__."../include/session_control.php"); //REMOVE THIS LINE IF SESSION_START() ALREADY EXIST AND INClUDE THE APIs UNDER THE FUNCTION
include(__DIR__."/../include/login_control.php");
include(__DIR__."/../fort42API/api.php");

}


$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
/*
$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('dev', true);
//$kernel = new AppKernel('dev', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
