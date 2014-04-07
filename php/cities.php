<?php

include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'City.class.php';
include_once CLASSES . 'App.class.php';

/**
 * Echoes a javascript array with the cities stored in the database
 * @param $uid the unique identifier of the app
 * @return json all City[] objects for the specific app
 */
function printSelectedCities($uid) {
    $cities = array();

    if (!empty($uid)) {
        Database::connect();

        if ($app = App::createFromDb($uid)) {

            foreach ($app->datasetIds as $datasetId) {

                $sqlCity = "SELECT * FROM cities JOIN city_datasets ON city_datasets.city_id=cities.id 
            WHERE dataset_id=" . $datasetId;

                foreach (Database::$dbh->query($sqlCity) as $city) {
                    $id = $city['city_id'];
                    $name = $city['name'];
                    $lat = $city['latitude'];
                    $lon = $city['longitude'];

                    $city = new City($id, $name, $lat, $lon, null);
                    if (!in_array($city, $cities)) {
                        $cities[] = $city;
                    }
                }
            }
        }
        Database::disconnect();
    }
    Util::printJsonObj($cities);
}

?>