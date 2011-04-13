<?php
/**
* Class representing a Currency
* @author Daniel Maison <daniel.maison@live.se>
* @author Markus Gerdau <markus.gerdau@gmail.com>
* @property productModel $products
*/

class currencyModel extends joz_activeRecordModel {
	
	public $tableName = 'pec_currencies';
	
	public $id;
	public $name;
	public $suffix;
	public $prefix;
	public $currencyCode;
	public $modifier;

}