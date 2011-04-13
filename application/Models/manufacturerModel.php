<?php
/**
* Class representing a Manufacturer
* @author Daniel Maison <daniel.maison@live.se>
* @author Markus Gerdau <markus.gerdau@gmail.com>
* @property productModel $products
*/
class manufacturerModel extends joz_activeRecordModel{
	
	public $primaryKey = 'id';
	public $tableName = 'pec_manufacturers';
	
	/*
	* Markup for relations. You might want to add '@property' tags in the class phpDoc.
	*/
	protected $relations = array(
				'products' => array(
							'class' => 'productModel',
							'key' => 'manufacturerId',
							'type' => self::HAS_MANY),
							);
	
	public $id;	
	public $name;
	public $logo;
	public $logoType;
}