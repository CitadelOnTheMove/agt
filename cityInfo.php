<?php

include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'City.class.php';





if (isset($_GET['cityName']) && isset($_GET['lat']) && isset($_GET['lon'])) {
    
  echo  City::getCityId($_GET['cityName'], $_GET['lat'], $_GET['lon']);
    
} else {
    /**
     * Echoes a json array with the cities stored in the database
     * @return json all City[] objects
     */
    $cities = array();
    Database::connect();


    $sqlCity = "SELECT * FROM cities";
    foreach (Database::$dbh->query($sqlCity) as $city) {
        $id = $city['id'];
        $name = $city['name'];
        $lat = $city['latitude'];
        $lon = $city['longitude'];

        $city = new City($id, $name, $lat, $lon, null);
        if (!in_array($city, $cities)) {
            $cities[] = $city;
        }
    }
    Database::disconnect();

    $arr = array('cities' => $cities);
    Util::printJsonObj($arr);
}


?>