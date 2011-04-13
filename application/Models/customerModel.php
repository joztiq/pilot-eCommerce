<?php
/**
* Class representing a Customer
* @author Daniel Maison <daniel.maison@live.se>
* @author Markus Gerdau <markus.gerdau@gmail.com>
* @property customCustomerFieldModel $customFields
*/
class customerModel extends joz_activeRecordModel {
	
	public $tableName = 'pec_customers';
	
	/*
	* Markup for relations. You might want to add '@property' tags in the class phpDoc.
	*/
	protected $relations = array(
				'customFields' => array(
							'class' => 'customCustomerFieldModel',
							'key' => 'customerId',
							'type' => self::HAS_MANY),
							);
	
	public $id;
	public $firstName;
	public $lastName;
	public $streetAddress;
	public $postalCode;
	public $city;
	public $phone;
	public $country;
	public $email;
	public $password;
	
	
}