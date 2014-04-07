<?php

/**
 * A simple class to represent geo coordinates
 *
 */
class GeoLocation {

    public $latitude;
    public $longitude;

    /**
     * Creates a new instance of the GeoLocation object
     * @param float $latitude the latitude of the point
     * @param float $longitude the longitude of the point
     */
    public function __construct($latitude, $longitude) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

}

?>