<?php
class categoryModel extends joz_activeRecordModel {
	
	public $primaryKey = 'id';
	public $tableName = 'pec_categories';
	
	/*
	* Markup for relations. You might want to add '@property' tags in the class phpDoc.
	*/
	protected $relations = array(
				'products' => array(
							'class' => 'productModel',
							'key' => 'id',
							'type' => self::HAS_MANY),
							);
	
	public $id;
	public $description;
	public $name;
	public $image;
	public $imageType;
	public $sortOrder;
}