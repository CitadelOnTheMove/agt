<?php

include_once CLASSES . 'GeoLocation.class.php';
include_once CLASSES . 'AppGeneratorException.class.php';

/**
 * Describes the location point position object to be used in a @see PoiLocationPoint
 */

class PoiLocationPointPos {
    /*
     * Member variables
     */

    public $srsName;
    public $posList;

    /**
     * Creates a new instance of the PoiLocationPointPos object
     * @param string $srsName the GIS standard used e.g. http://www.opengis.net/def/crs/EPSG/0/4326
     * @param string $posList space seperated pair values of lat/lng 
     */
    public function __construct($srsName, $posList) {
        $this->srsName = $srsName;
        $this->posList = $posList;
    }

    /**
     * Returns a new instance of GeoLocation
     * @param array $assocArray an associative array representation of the object
     * @return GeoLocation[]
     */
    public function getCoords() {
        $ret = array();

        $geoPairs = explode(',', $this->posList);
        foreach ($geoPairs as $geoPair) {
            $pointCoords = explode(' ', $geoPair);
            $ret[] = new GeoLocation($pointCoords[0], $pointCoords[1]);
        }
        return $ret;
    }

    /**
     * Factory method that returns a new instance of PoiLocationPointPos
     * @param array $assocArray an associative array representation of the object
     * @return PoiLocationPointPos|boolean a PoiLocationPointPos instance or false on failure
     */
    public static function createFromArray($assocArray) {
        if (isset($assocArray['srsName']) && isset($assocArray['posList'])) {
            // if (!is_numeric($assocArray['posList']))

            $geoPairs = explode(',', $assocArray['posList']);
            foreach ($geoPairs as $geoPair) {
                $pointCoords = explode(' ', $geoPair);
            }
            if (!is_numeric($pointCoords[0]) || (!is_numeric($pointCoords[1]))) {
                throw(new AppGeneratorException("Please make sure that your dataset contains the right coords!!"));
            }
            return new PoiLocationPointPos($assocArray['srsName'], $assocArray['posList']);
        }
        return false;
    }

}

?>