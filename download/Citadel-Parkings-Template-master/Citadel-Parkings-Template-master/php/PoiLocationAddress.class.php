<?php

class PoiLocationAddress {
	/*
	 * Memeber variables
	 */
	public $value;
	public $postal;
	public $city;
	
	/**
	 * @param string $value the street name of the address
	 * @param string $postal the postal code of the address
	 * @param string $city the city that this address belongs to
	 */
	public function __construct($value, $postal, $city) { 
		$this->value = $value;
		$this->postal = $postal;
		$this->city = $city;		
	}	
	
	/**
	 * Factory method that returns a new instance of PoiLocationAddress
	 * @param array $assocArray an associative array representation of the object
	 * @return PoiLocationAddress|boolean a PoiLocationAddress instance or false on failure
	 */
	public static function createFromArray($assocArray) {
		if(isset($assocArray['value']) && isset($assocArray['postal']) && isset($assocArray['city'])) 
			return new PoiLocationAddress($assocArray['value'], $assocArray['postal'], $assocArray['city']);
		return false;
	}
	
// 	public function getValue() {
// 		return $this->value;
// 	}
	
// 	public function setValue($value) {
// 		$this->$value = $value;
// 	}
	
// 	public function getPostal() {
// 		return $this->postal;
// 	}
	
// 	public function setPostal($postal) {
// 		$this->postal = postal;
// 	}
	
// 	public function getCity() {
// 		return $this->city;
// 	}
	
// 	public function setCity($city) {
// 		$this->city = $city;
// 	}
}
?>