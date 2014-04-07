<?php

/**
 * Application filters are used to filter which markers the user 
 * will see on the map
 */
class Filter {

     /*
     * Member variables are public in order 
     * to be serialized properly by json_encode
     */
    public $name;
    public $selected;
    public $type;
    public $cityId;
    public $isVisible;
    public $poisCounter;

    /**
     * Creates a new instance of the Filter object
     * @param string $name the name of the filter
     * @param boolean $selected indicates if the filter should be selected by default
     * @param string $type the dataset type
     * @param int $cityId the id of the city
     * @param boolean $isVisible indicates if the filter should be visible or not
     * @param int $poisCounter counts the number of pois per dataset category
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