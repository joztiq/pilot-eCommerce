<?php
class joz_xmlModel extends DOMDocument{
	
	/**
	 * Holding a logger for this class
	 * @var Logger
	 */
	protected $logger;
	
	public function __construct($version, $encoding)
	{
		parent::__construct($version, $encoding);
		$this->logger = Logger::getLogger('joztiq.application.models.'.get_class($this));
	}
	
}