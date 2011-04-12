<?php
class taxModel extends joz_activeRecordModel {
	
	public $primaryKey = 'id';
	public $tableName = 'pec_taxes';
	
	/*
	* Markup for relations. You might want to add '@property' tags in the class phpDoc.
	*/
	protected $relations = array(
				'products' => array(
							'class' => 'productModel',
							'key' => 'taxId',
							'type' => self::HAS_MANY),
							);
	
	public $id;
	public $name;
	public $rate;
	
}