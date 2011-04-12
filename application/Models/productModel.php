<?php
/**
* Class representing ...
* @author Daniel Maison <daniel.maison@auriga.se>
* @author Markus Gerdau <markus.gerdau@auriga.se>
*
*/
class productModel extends joz_activeRecordModel{

	public $primaryKey = 'id';
	public $tableName = 'pec_products';
	
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
	public $name;
	public $shortDescription;
	public $longDescription;
	public $SKU;
	public $price;
	public $vatId;
	public $manufacturerId;
	public $weight;
	public $status;
	public $quantity;
	public $dateAdded;
	public $useQuantity;
	
}