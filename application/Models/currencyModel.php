<?php
class currencyModel extends joz_activeRecordModel {
	
	public $tableName = 'pec_currencies';
	
	public $id;
	public $name;
	public $suffix;
	public $prefix;
	public $currencyCode;
	public $modifier;

}