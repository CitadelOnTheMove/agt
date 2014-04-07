<?php
/**
 * Describes the location address object to be used in a @see Poi
 */
class PoiLocationAddress {
    /*
     * Memeber variables
     */

    public $value;
    public $postal;
    public $city;

    /**
     * Creates a new instance of the PoiLocationAddress object
     * @param string $value the street name of the address
     * @param string $postal the postal code of the address
     * @param string $city the city that this address belongs to
     */
    public function
    __construct($value, $postal, $city) {
        $this->value = $value;
        $this->postal = $postal;
        $this->city = $city;
    }

    /**
     * Factory method that returns a new instance of PoiLocationAddress
     * @param array $assocArray an associative array representation of the object
     * @return PoiLocationAddress|boolean a PoiLocationAddress instance or false on failure
     */
    public static function createFromArray($assocArray) {

        $city = isset($assocArray['city']) ? $assocArray['city'] : "undefined";
        $address = isset($assocArray['value']) ? $assocArray['value'] : "undefined";
        $postal = isset($assocArray['postal']) ? $assocArray['postal'] : "undefined";
        return new PoiLocationAddress($address, $postal, $city);
    }

}

?>