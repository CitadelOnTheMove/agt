<?php

include_once CLASSES.'PoiLocationPoint.class.php';
include_once CLASSES.'PoiLocationAddress.class.php';

/**
 * Describes the location object to be used in a @see Poi
 */
class PoiLocation {
	
	public $point; 	// the PoiLocationPoint.class object 
	public $address;	// the PoiLocationAddress.class object
	
	/**
	 * @param PoiLocationPoint $point
	 * @param PoiLocationAddress $address
	 */
	public function __construct($point , $address) {
		$this->point = $point;
		$this->address = $address;		
	}
	
	/**
	 * Initialize object from db
	 * @param int $poiId the id of the poi
	 */
	public static function createFromDb($poiId) {
		$sql = "SELECT *
				FROM poiLocations
				WHERE poi_id = :id";
		$sqlParams[':id'] = $poiId;
	
		try {
			$sth = Database::$dbh->prepare($sql);
			$sth->execute($sqlParams);
		} catch (Exception $e) {
			if (DEBUG) $sth->debugDumpParams();
			Util::throwException(__FILE__, __LINE__, __METHOD__, "select from poiLabels failed", $e->getMessage(), $e);
		}
	
		if(!($result = $sth->fetch(PDO::FETCH_ASSOC))) {
			Util::throwException(__FILE__, __LINE__, __METHOD__, "No poiLabels found");
			return null;
		}
		
		return new PoiLocation($result['point'], $result['address']);
	}
	
	/**
	 * Factory method that returns a new instance of PoiLocation
	 * @param array $assocArray an associative array representation of the object
	 * @return PoisLocation|boolean a PoiLocation instance or false on failure
	 */
	public static function createFromArray($assocArray) {
		if(isset($assocArray['point']) && isset($assocArray['address'])) {
			return new PoiLocation(PoiLocationPoint::createFromArray($assocArray['point']), 
									PoiLocationAddress::createFromArray($assocArray['address']));
		}
		return false;		
	}
	
// 	public function getPoint() {
// 		return $this->point;
// 	}
	
// 	public function setPoint($point) {
// 		$this->point = $point;
// 	}
	
// 	public function getAddress() {
// 		return  $this->address;
// 	}
	
// 	public function setAddress($address) {
// 		$this->address = $address;
// 	}	
}
?>