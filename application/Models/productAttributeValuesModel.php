<?php
/**
* Class representing a Product attribute value
* @author Daniel Maison <daniel.maison@live.se>
* @author Markus Gerdau <markus.gerdau@gmail.com>
* @property productAttributeModel $attribute
*/
class productAttributeValuesModel extends joz_activeRecordModel {
	
	public $primaryKey = 'id';
	public $tableName = 'pec_productAttributeValues';
	
	/*
	* Markup for relations. You might want to add '@property' tags in the class phpDoc.
	*/
	protected $relations = array(
				'attribute' => array(
							'class' => 'productAttributeModel',
							'key' => 'attributeId',
							'type' => self::BELONGS_TO),
							);
	
	public $id;
	public $attributeId;
	public $priceModifier;
	public $quantity;
	public $value;
	public $status;
	
}