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

    public static function saveNewCity($cityName, $latitude, $longitude) {

        $query = "SELECT * FROM cities WHERE name= :city";
        $sqlParams1[":city"] = $cityName;
        try {
            $sth1 = Database::$dbh->prepare($query);
            $sth1->execute($sqlParams1);
            if ($sth1->rowCount() == 0) {

                $sql1 = "INSERT INTO cities VALUES(null, :city, :latitude, :longitude)";
                $sqlParams1 = array(':city' => $cityName,
                    ':latitude' => round($latitude, 6)  ,
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