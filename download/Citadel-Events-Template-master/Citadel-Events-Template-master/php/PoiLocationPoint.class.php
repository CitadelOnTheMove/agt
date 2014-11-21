<?php

include_once CLASSES.'PoiLocationPointPos.class.php';


class PoiLocationPoint {	
	/*
	 * Member variables
	 */
	public $term;	// the type of the position e.g. centroid
	public $pos;	// the PoiLocationPointPos object
	
	
	/**
	 * @param string $term the type of the position e.g. centroid
	 * @param PoiLocationPointPos $pos th object containing the lat/lng pairs
	 */
	public function __construct($term, $pos) {
		$this->term = $term;
		$this->pos = $pos;
	}
	
	/**
	 * Factory method that returns a new instance of PoiLocationPoint
	 * @param array $assocArray an associative array representation of the object
	 * @return PoiLocationPoint|boolean a PoiLocationPoint instance or false on failure
	 */
	public static function createFromArray($assocArray) {
		if(isset($assocArray['term']) && isset($assocArray['pos']))
			return new PoiLocationPoint($assocArray['term'], PoiLocationPointPos::createFromArray($assocArray['pos']));
		return false;
	}
	
	
// 	public function getTerm() {
// 		return $this->term;
// 	}
	
// 	public function setTerm($term) {
// 		$this->term = $term;
// 	}
	
// 	public function getPos() {
// 		return $this->pos;
// 	}
	
// 	public function setPos($pos) {
// 		$this->pos = $pos;
// 	} 
}

?>