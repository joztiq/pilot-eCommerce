<?php
class joz_languageHandler{
	
	/**
	 * Holding a logger for this class
	 * @var Logger
	 */
	protected $logger;
	protected $_ini;
	protected $_values;
	protected $_file;
	protected $_defaultLanguage;
	protected $_currLang;
	
	public function __construct()
	{
		$this->logger = Logger::getLogger('joztiq.components.'.get_class($this));
		$this->_file = APP_PATH."Configs".DS."languages.ini";
		$config = joz_config::getInstance();
		if($config->useLanguageHandler == true)
		{
			if(!file_exists($this->_file))
				throw new SBSException('Could not find languages config file. File must reside in application/Configs/ and should be named languages.ini');
			$this->_ini = parse_ini_file($this->_file, true);
			if($this->_ini == null)
			{
				throw new SBSException('Could not parse languages.ini');
			}
			$this->_defaultLanguage = $config->defaultLanguage;
			if($this->_defaultLanguage == null)
			{
				throw new SBSException("No default language set in config. use 'defaultLanguage'");
			}
			$this->setLanguage($this->getBrowserLanguage());
		}else{
			$this->logger->debug("Not using language handler...");
		}
	}
	
	public function setLanguage($lang)
	{ 
		
		if(!array_key_exists($lang , $this->_ini))
		{
			if($lang == $this->_defaultLanguage)
				throw new SBSException("Could find the section for default language in {$this->_file}.");
			$lang = $this->_defaultLanguage;
		}
			
		foreach($this->_ini[$lang] as $k => $v)
		{
			$this->_values[$k] = $v;
		}
		$this->_currLang = $lang;
	}
	
	/** uses the magic get so that config parrams are easy to get
	 * in a property kind way but still read only.
	 * @param string $name
	 */	
	public function __get($name)
	{
		if(isset($this->_values[$name]))
			return $this->_values[$name];
	}
	
	public function getCurrentLanguage()
	{
		return $this->_currLang;
	}
	
	public static function getBrowserLanguage()
	{
		return strtoupper(substr(@$_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
	}
}