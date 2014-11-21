<?php

class Filter {
	
	public $name;
	public $selected;
	
	/**
	 * 
	 * @param string $name the name of the filter
	 * @param boolean $selected indicates if the filter should be selected by default
	 */
	public function __construct($name, $selected) {
		$this->name = $name;
		$this->selected = $selected;
	}
}