<?php
/**
* Class representing a Product
* @author Daniel Maison <daniel.maison@live.se>
* @author Markus Gerdau <markus.gerdau@gmail.com>
* @property categoryModel $category
* @property manufacturerModel $manufacturer
* @property taxModel $tax
*/
class productModel extends joz_activeRecordModel{

	public $primaryKey = 'id';
	public $tableName = 'pec_products';
	
	/*
	* Markup for relations. You might want to add '@property' tags in the class phpDoc.
	*/
	protected $relations = array(
				'category' => array(
							'class' => 'categoryModel',
							'key' => 'categoryId',
							'type' => self::BELONGS_TO),
				'manufacturer' => array(
							'class' => 'manufacturerModel',
							'key' => 'manufacturerId',
							'type' => self::BELONGS_TO),
				'tax' => array(
							'class' => 'taxModel',
							'key' => 'taxId',
							'type' => self::HAS_ONE)
							);
	
	public function getLinkName()
	{
		return str_replace(" ", "_", $this->name);
	}

	public function checkQuantityStatus()
	{
		if($this->useQuantity && $this->quantity == 0)
		{
			//$this->status = 
		}
	}
	
	public $id;
	public $categoryId;
	public $manufacturerId;
	public $name;
	public $shortDescription;
	public $longDescription;
	public $SKU;
	public $price;
	public $taxId;
	public $manufacturerId;
	public $weight;
	public $status;
	public $quantity;
	public $dateAdded;
	public $useQuantity;
	
}