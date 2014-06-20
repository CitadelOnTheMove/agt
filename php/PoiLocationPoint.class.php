<?php

include_once CLASSES . 'PoiLocationPointPos.class.php';

/**
 * Describes the location point object to be used in a @see PoiLocation
 */
class PoiLocationPoint {
    /*
     * Member variables
     */

    public $term;
    public $pos;

    /**
     * Creates a new instance of the PoiLocationPoint object
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
        if (isset($assocArray['term']) && isset($assocArray['pos']))
            return new PoiLocationPoint($assocArray['term'], PoiLocationPointPos::createFromArray($assocArray['pos']));
        return false;
    }

}

?>