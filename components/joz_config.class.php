<?php
class joz_config{
	/**
	 * Private var holding an instance of the class
	 * @var joz_config
	 */
	private static $_instance;
	/**
	 * Private var holding the parsed config file
	 * @var array ini
	 */
	private $_ini;

	/**
	 * Private var holding the logger
	 * @var Logger logger
	 */
	private $logger;
	/**
	 * Private array holding the parsed globals array
	 * @var array
	 */
	private $_globals;
	/**
	 * Private var holding the parsed config defaults
	 * @var array defaults
	 */
	private static $_defaults = array(
									'showErrorTrace' => true,
									'applicationName' => "Joztiq application",
									);
	/**
	 * Private var holding the parsed config file values
	 * @var array of strings
	 */	
	private $_values;
	
	/**
	 * @var string activeEnvironment
	 */
	private $_activeEnvironment;
	
	/**
	 * uses the magic get so that config parrams are easy to get
	 * in a property kind way but still read only.
	 * @param string $name
	 */
	public function __get($name)
	{
		if(isset($this->_values[$name]))
			return $this->_values[$name];
	}
	/**
	 * private construct enforces singleton pattern
	 * 
	 * function will parse main config and set application values
	 */
	private function __construct()
	{
		$this->logger = Logger::getLogger("joztiq.framework.config");
		$file = APP_PATH."Configs".DS."main.ini";
		if(!file_exists($file))
			throw new SBSException('Could not find main config file. File must reside in application/Configs/ and should be named main.ini');
		$this->_ini = parse_ini_file($file, true);
		if($this->_ini == null)
		{
			throw new SBSException('Could not parse main.ini');
		}
		$this->_values = array($this->_defaults);
		if(!array_key_exists('application' , $this->_ini))
			throw new SBSException("Could not find 'application' section of config file.");
		foreach($this->_ini['application'] as $k => $v)
		{
			$this->_values[$k] = $v;
		}
	}
	/**
	 * Set new environment
	 * 
	 * Function sets values from the given evironment
	 * note that environment values overrides application values
	 * @param string $env
	 */
	public function setEnvironment($env)
	{
		if(!array_key_exists("environment_".$env , $this->_ini))
			throw new SBSException('Unknown environment '.$env);
		foreach($this->_ini["environment_".$env] as $k => $v)
		{
			$this->_values[$k] = $v;
		}
		$this->_activeEnvironment = $env;
	}
	/**
	 * Get the active environment
	 * @return string activeEnvironment
	 */
	public function getEnvironment()
	{
		return $this->_activeEnvironment;
	}
	
	public function getRoutes()
	{
		if(!array_key_exists('routes' , $this->_ini))
			throw new SBSException("No 'routes' section found in config file");
		return $this->_ini['routes'];
	}
	/**
	 * function to get pre set globals from the config
	 * @param void
	 * @return array
	 */
	public function getGlobals()
	{
		if(!array_key_exists('globals' , $this->_ini))
			throw new SBSException("No 'globals' section found in config file");
		return $this->_ini['globals'];
	}
	
	/**
	 * Call hook for this class
	 * $param void
	 * @return joz_config instance of class
	 */
	public static function getInstance()
	{
		    if (!isset(self::$_instance)) 
		    {
            	$c = __CLASS__;
            	self::$_instance = new $c;
        	}
        return self::$_instance;
	}
	
	/**
	 * function to get a pre set global variable from the config
	 * @param string
	 * @return string
	 */
	public function getGlobalVar($key)
	{
		if(!isset($this->_globals))
		{
			$this->_globals = $this->getGlobals();
		}
		try{
			return $this->getGlobal($key);
		}catch(SBSException $e)
		{
			$this->logger->error('Error fetching global var' , $e);
			return $key;
		}
		
	}
	
	private function getGlobal($key)
	{
		if(isset($this->_globals[$key]))
		{
			return $this->_globals[$key];
		}else{
			throw new SBSException('Trying to access undeclared global var.');
		}
	}
	
	public function getVar($name)
	{
		if(isset($this->_values[$name]))
			return $this->_values[$name];
	}
	
}