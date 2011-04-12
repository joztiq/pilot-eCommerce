<?php
interface joz_IsoapComplexType{
	
	
	/**
	 * Returns array with type descriptions, eg id => 'int', name => 'string'
	 * @return array assoc array describing the classes types
	 */
	public static function getTypes();
	
	const string = 'xsd:string';
	const int = 'xsd:integer';
	const float = 'xsd:float';
	const bool = 'xsd:boolean';
}