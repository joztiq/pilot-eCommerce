<?php
class joz_formModel extends joz_model{

	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * function assign
	 *
	 * function used to mass-assign values to an object
	 * @param array $vals
	 * @return void
	 */
	public function assign(array $vals)
	{
		foreach($vals as $k => $v)
		{
			if(property_exists($this , $k))
			{
				$this->$k = $v;
			}
		}
	}
}