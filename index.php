<?php
//require('/var/www/pqp/classes/PhpQuickProfiler.php');
//$prof = new PhpQuickProfiler(PhpQuickProfiler::getMicroTime());

/** 
 * Joztiq Framework 
 * Framework loader - acts as a single point of access to the Framework 
 * 
 * @version 0.1 
 * @author Daniel Maison
 * @author Markus Gerdau
 */  

//Define some usefull constants
define("DS" , DIRECTORY_SEPARATOR);
define("ROOT_PATH" , dirname(__FILE__).DS);
define("WEB_PATH" , ROOT_PATH."public".DS);
define("APP_PATH" , ROOT_PATH."application".DS);
define("FW_PATH" , ROOT_PATH."components".DS);

/**
 * Catches uncaught exceptions and send them for logging (mail/file etc)
 * @param Exception $exception
 */

function exception_handler($exception) {
  Logger::getRootLogger()->error("Uncaught exception:\n $exception \n");
// echo $exception;
}
/*
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");
*/
set_exception_handler('exception_handler');
/** 
 * Magic autoload function 
 * used to include the appropriate -controller- files when they are needed 
 * @param String the name of the class
 */  
function std_autoload( $class_name )
{  
    //Check framework files
	if(file_exists(FW_PATH.$class_name.".class.php"))
    {
    	include(FW_PATH.$class_name.".class.php");
    	return 0;
    }
    //Check Controllers
	if(file_exists(APP_PATH."Controllers".DS.$class_name.".php"))
    {
    	include(APP_PATH."Controllers".DS.$class_name.".php");
    	return 0;
    }
    //Check Models
	if(file_exists(APP_PATH."Models".DS.$class_name.".php"))
    {
    	include(APP_PATH."Models".DS.$class_name.".php");
    	return 0;
    }
    //Recursive load of "classes" OBS slower
	$class = $class_name.'.class.php';
	$rit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(FW_PATH));
	foreach ($rit as $entry)
	{
		if ($class == $entry->getFileName())
		{
			include($entry->getPathname());
			return 0;
		}
		if ($class_name.".php" == $entry->getFileName())
		{
			include($entry->getPathname());
			return 0;
		}
	}
		$rit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(APP_PATH.'lib'.DS));
		foreach ($rit as $entry)
		{
			if ($class == $entry->getFileName())
			{
				include($entry->getPathname());
				return 0;
			}
			if ($class_name.".php" == $entry->getFileName())
			{
				include($entry->getPathname());
				return 0;
			}
		}
//	if(file_exists(FW_PATH."phpseclib".DS.'Crypt'.DS.$class_name.".php"))
//    {
//    	include(FW_PATH."phpseclib".DS.'Crypt'.DS.$class_name.".php");
//    	return 0;
//    }
}
spl_autoload_register('std_autoload');

$joztiq = Joztiq::app();
if(getenv('APPLICATION_ENV') != false)
{
	$joztiq->config->setEnvironment(getenv('APPLICATION_ENV'));
	
}

//If run from CLI
if(isset($argc))
{
	ini_set("display_errors", "1");
	$joztiq->RunCLI($argv);
}else { //Run from web
$joztiq->Run();
}
//$prof->display();
