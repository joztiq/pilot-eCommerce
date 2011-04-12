<?php
/**
 * Interface for models with validation rules
 *
 */
interface joz_Ivalidation{
	/**
	 * @return array of joz_validationRules
	 * 
	 */
	function getRules();
	
}