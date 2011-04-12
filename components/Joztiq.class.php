<?php
/**
 * Main class of the Joztiq framework
 *  
 * @author Daniel Maison
 * @author Markus Gerdau
 *
 */

class Joztiq
{
	
	/**
	 * public instance of the httoRequest class
	 * @var joz_httpRequest
	 */
	public $httpRequest;
	/**
	 * public instance of the frontController class
	 * @var joz_frontController $fc
	 */
	public $fc;
	/**
	 * public instance of the config
	 * @var joz_config
	 */
	public $config;
	
	/**
	 * public instance of the session handler
	 * @var joz_sessionHandler $session
	 */
	public $session;
	
	/**
	 * Private var holding an instance of the class
	 * @var Joztiq
	 */
	private static $_instance;
	
	/**
	 * Holding a instance of logger for this class
	 * @var Logger
	 */
	protected $logger;
	/**
	 * Holding a instance of languageHandler
	 * @var joz_languageHandler
	 */
	public $languageHandler;
	
	/**
	 * Run in cli mode?
	 * @var bool true if Joztiq::RunCLI();
	 */
	public static $cli = false;
	
	
	/**
	 * This class uses the singleton pattern. Use Joztiq::app()
	 * @param void
	 * @return void
	 */
	private function __construct()
	{
		try
		{
			include(FW_PATH.'log4php'.DS.'Logger.php');
			if(file_exists(APP_PATH.'Configs'.DS.'logging.properties.'.getenv('APPLICATION_ENV')))
			{
				Logger::configure(APP_PATH.'Configs'.DS.'logging.properties.'.getenv('APPLICATION_ENV'));
			}else{
				Logger::configure(APP_PATH.'Configs'.DS.'logging.properties');
			}
			$this->logger = Logger::getLogger('joztiq.framework.joztiq');
			$this->config = joz_config::getInstance();
			$this->httpRequest = joz_httpRequest::getInstance();			
			$this->fc = joz_frontController::getInstance();
			$this->languageHandler = new joz_languageHandler();
			
		}catch(SBSException $e)
		{
			$errorMsg = "<html><head/><body><h2>Some bad coding man...</h2><pre>$e</pre><body/></html>";
			die(str_ireplace(ROOT_PATH, "", $errorMsg));
		}
		
	}
	/**
	 * Hook for application instance
	 * @param void
	 * @return Joztiq
	 */
	public static function app()
	{
		if (!isset(self::$_instance)) 
		    {
            	$c = __CLASS__;
            	self::$_instance = new $c;
        	}
        return self::$_instance;
	}
	
	/**
	 * Locolized string
	 * Function used to get a locolized string from the defined language handler
	 * @param string $string
	 * @return string localizedString
	 */
	public static function ls($string)
	{
		return self::app()->languageHandler->$string;
	}
	
	/**
	 * Main appliaction entry point
	 * 
	 */
	public function Run()
	{
			if($this->config->sessionHandler != null)
			{
				$sessionHandler = $this->config->sessionHandler;
				$this->session = $sessionHandler::getInstance();
			}else{
				$this->logger->debug("No sessionHandler defined in config.");
			}
		foreach($this->config->getRoutes() as $route)
		{
			$this->fc->addRoute(new $route());
		}
		$this->fc->parseRoutes();
		$this->fc->dispatch();
		session_write_close();
	}
	
	/**
	 * Main appliaction entry point for CLI usage
	 * @param array $argv
	 */
	public function RunCLI($argv)
	{		
		self::$cli = true;
		$args = $this->parseArgs($argv);
		if(!isset($args['controller']) || !isset($args['action']))
		{
			echo "Controller and action must be set. Useage: index.php --action=someAction --controller=myController --optionalArg=veryImportantArgument";
			throw new SBSException("Controller or action not set for CLI usage.".print_r($args));
		}
		$args['cli'] = true;
		$this->fc->setController($args['controller']);
		$this->fc->setAction($args['action']);
		$this->fc->setActionParams($args);
		$this->fc->dispatch();
	}
	
	/**
	 * Internal function used to parse CLI arguments
	 * usage:
	 * test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
     * > 'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
     *   [0]       => "plain-arg"
     *   ["foo"]   => true
     *   ["bar"]   => "baz"
     *   ["funny"] => "spam=eggs"
     *   ["also-funny"]=> "spam=eggs"
     *   [1]       => "plain arg 2"
     *   ["a"]     => true
     *   ["b"]     => true
     *   ["c"]     => true
     *   ["k"]     => "value"
     *   [2]       => "plain arg 3"
     *   ["s"]     => "overwrite"
     *   
	 * @param array $argv
	 */
	private function parseArgs($argv){
	    array_shift($argv); $o = array();
	    foreach ($argv as $a){
	        if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
	            if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
	            else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
	        else if (substr($a,0,1) == '-'){
	            if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
	            else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
	        else { $o[] = $a; } }
	    return $o;
	}

	
	
}