<?php

include_once CLASSES.'GeoLocation.class.php';

 class PoiLocationPointPos {
 	
 	/*
 	 * Member variables
 	 */
 	public $srsName; 	// the GIS standard used e.g. http://www.opengis.net/def/crs/EPSG/0/4326
 	public $posList;	// space seperated pair values of lat/lng 

 	/**
 	 * @param string $srsName the GIS standard used e.g. http://www.opengis.net/def/crs/EPSG/0/4326
 	 * @param string $posList space seperated pair values of lat/lng 
 	 */
 	public function __construct($srsName, $posList) {
 		$this->srsName = $srsName;
 		$this->posList = $posList;
 	}
 	
 	
 	public function getCoords() {
 		$ret = array();
 		
 		$geoPairs = explode(',', $this->posList);
 		foreach($geoPairs as $geoPair) {
 			$pointCoords = explode(' ', $geoPair); 			
 			$ret[]= new GeoLocation($pointCoords[0], $pointCoords[1]);
 		}
 		return $ret;
 	}
 	
 	public static function createFromDb() {
 		
 	}
 	
 	/**
 	 * Factory method that returns a new instance of PoiLocationPointPos
 	 * @param array $assocArray an associative array representation of the object
 	 * @return PoiLocationPointPos|boolean a PoiLocationPointPos instance or false on failure
 	 */
 	public static function createFromArray($assocArray) {
 		if(isset($assocArray['srsName']) && isset($assocArray['posList']))
 			return new PoiLocationPointPos($assocArray['srsName'], $assocArray['posList']);
 		return false; 		
 	}
 	
//  	public function getSrsName() {
//  		return $this->srsName;
//  	}
 	
//  	public function setSrsName($srsName) {
//  		$this->srsName = $srsName;
//  	}
 	
//  	public function getPosList() {
//  		return $this->posList;
//  	}
 	
//  	public function setPosList($posList) {
//  		$this->posList = $posList; 		
//  	} 	
 } 
 ?>