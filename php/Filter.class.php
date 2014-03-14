<?php

class Filter {

    public $name;
    public $selected;
    public $type;
    public $cityId;
    public $isVisible;
    public $poisCounter;

    /**
     * 
     * @param string $name the name of the filter
     * @param boolean $selected indicates if the filter should be selected by default
     */
    public function __construct($name, $selected, $type, $cityId, $isVisible, $poisCounter) {
        $this->name = $name;
        $this->selected = $selected;
        $this->type = $type;
        $this->cityId = $cityId;
        $this->isVisible = $isVisible;
        $this->poisCounter = $poisCounter;
    }
}