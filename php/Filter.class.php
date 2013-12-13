<?php

class Filter {
	
	public $name;
	public $selected;
        public $type;
	
	/**
	 * 
	 * @param string $name the name of the filter
	 * @param boolean $selected indicates if the filter should be selected by default
	 */
	public function __construct($name, $selected, $type) {
		$this->name = $name;
		$this->selected = $selected;
                $this->type = $type;
	}
}