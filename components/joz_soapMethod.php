<?php
class joz_soapMethod
{
	/**
	 * Method name
	 * @var string
	 */
	private $methodName;
	/**
	 * Belongs to class
	 * @var string
	 */
	private $className;
	
	/**
	 * input params
	 * @var joz_soapVal[]
	 */
	private $params = array();
	/**
	 * return params
	 * @var joz_soapVal[]
	 */
	private $return = array();
	/**
	 * doc
	 * @var string
	 */
	private $documentation;
	
	public function setMethod($method)
	{
		$this->methodName = $method;
	}
	
	public function getMethod()
	{
		return $this->methodName;
	}
	
	public function setClass($class)
	{
		$this->className = $class;
	}
	
	public function getClass()
	{
		return $this->className;
	}
	
	public function addInputParam(joz_soapVal $val)
	{
		$this->params[] = $val;
	}
	public function addInputParams($values)
	{
		foreach($values as $val)
		{
			if(!$val instanceof joz_soapVal)
			{
				throw new InvalidArgumentException("Input params must be of type joz_soapVal or child of named class");
			}
		}
		$this->params = array_merge($this->params,$values);
	}
	
	public function addOutputParam(joz_soapVal $val)
	{
		$this->return[] = $val;
	}
	
	public function getInputParams()
	{
		return $this->params;
	}
	
	public function getOutputParams()
	{
		return $this->return;
	}
	
	public function setDocumentation($doc)
	{
		$this->documentation = $doc;
	}
	
	public function getDocumentation()
	{
		return $this->documentation;
	}
}