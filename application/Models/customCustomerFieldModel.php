<?php
/**
* Class representing a Custom 'customer field'
* @author Daniel Maison <daniel.maison@live.se>
* @author Markus Gerdau <markus.gerdau@gmail.com>
* @property productModel $products
*/
class customCustomerFieldModel extends joz_activeRecordModel {
	
	public $tableName = 'pec_customCustomerFields';
	
	public $id;
	public $name;
	public $customerId;
	
}