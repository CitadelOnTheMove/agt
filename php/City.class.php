<?php

class City {
	public $id;
	public $name;
	public $lat;
        public $lon;
	
	
	public function __construct($id, $name, $lat, $lon) {
                $this->id = $id;
		$this->name = $name;
		$this->lat = $lat;
                $this->lon = $lon;
	}
}