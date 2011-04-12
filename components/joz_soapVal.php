<?php
class joz_soapVal
{
	
	protected $name;
	protected $type;
	
	const INT = 'xsd:integer';
	const STRING = 'xsd:string';
	const BOOL = 'xsd:boolean';
	const FLOAT = 'xsd:float';
	
	public function __construct($name , $type)
	{
		$this->name = $name;
		$this->type = $type;
	}
	
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function gettype()
	{
		return $this->type;
	}
}