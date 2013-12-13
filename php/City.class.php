<?php

class City {
	
	public $name;
	public $lat;
        public $lon;
	
	/**
	 * 
	 * @param string $name the name of the filter
	 * @param boolean $selected indicates if the filter should be selected by default
	 */
	public function __construct($name, $lat, $lon) {
		$this->name = $name;
		$this->lat = $lat;
                $this->lon = $lon;
	}
}