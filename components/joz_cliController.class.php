<?php
abstract class joz_cliController{
	/**
	 * Holding passed params
	 * @var Array passed params
	 */
	public $params = array();
	
	/**
	 * Holding a logger for this class
	 * @var Logger
	 */
	protected $logger;
	
	public function __construct()
	{
		$this->logger = Logger::getLogger('joztiq.application.controllers.'.get_class($this));
		if(!Joztiq::$cli)
		{
			$this->logger->warn("CLI-controller not runned from CLI!");
			throw new RuntimeException("CLI-controller not runned from CLI!");
		}
	}
	
	/**
	 * Function runs before any actions
	 */
	public function before()
	{
		
	}
	
	/**
	 * Function runs after any actions
	 */
	public function after()
	{
		
	}
}