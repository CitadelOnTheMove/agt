<?php

/**
 * The city we have dataset for
 */
class City {

     /*
     * Member variables are public in order 
     * to be serialized properly by json_encode
     */

    public $id;
    public $name;
    public $lat;
    public $lon;
    public $datasets;

    
    /**
     * Creates a new instance of the City object
     * @param int $id the unique identifier of the city
     * @param string $name the name of the city
     * @param double $lat the latitude coordinations  of the city's center
     * @param double $lon the longitude coordinations of the city's center
     * @param int $datasets the datasets' ids of the city
     */
    public function __construct($id, $name, $lat, $lon, $datasets) {
        $this->id = $id;
        $this->name = $name;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->datasets = $datasets;
        
    }

     /**
     * Saves the City instance to the database
     * @param string $cityName the name of the city
     * @param double $lat the latitude coordinations  of the city's center
     * @param double $lon the longitude coordinations of the city's center
     * @return true on success of false otherwise
     */
    public static function saveNewCity($cityName, $latitude, $longitude) {

        $query = "SELECT * FROM cities WHERE name= :city";
        $sqlParams1[":city"] = $cityName;
        try {
            $sth1 = Database::$dbh->prepare($query);
            $sth1->execute($sqlParams1);
            if ($sth1->rowCount() == 0) {

                $sql1 = "INSERT INTO cities VALUES(null, :city, :latitude, :longitude)";
                $sqlParams1 = array(':city' => $cityName,
                    ':latitude' => round($latitude, 6),
                    ':longitude' => round($longitude, 6));

                try {
                    $sth2 = Database::$dbh->prepare($sql1);
                    $sth2->execute($sqlParams1);

                    $newCityId = Database::$dbh->lastInsertId();
                    return $newCityId;
                } catch (Exception $e) {
                    if (DEBUG)
                        $sth2->debugDumpParams();
                    Util::throwException(__FILE__, __LINE__, __METHOD__, "insert into cities failed", $e->getMessage(), $e);
                    return false;
                }
            } //end if
        } catch (Exception $e) {
            if (DEBUG)
                $sth1->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "new city query failed", $e->getMessage(), $e);
        }
    }

}