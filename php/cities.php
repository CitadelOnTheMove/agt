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
       // Database::connect();

        //if ($app = App::createFromDb($uid)) {
        // We will call a service that will give us a list of cities, based on the datasetIds
            // we provide it with
            Database::connect();
            $app = App::createFromDb($uid);
            Database::disconnect();
            $datasetIdsQueryString = "?format=json&datasetIds=";
            foreach ($app->datasetIds as $datasetId) {
                $datasetIdsQueryString .= $datasetId . ",";
            }
            // Removing the trailing comma
            $datasetIdsQueryString = rtrim($datasetIdsQueryString, ",");

            // Setting proxy settings
            if (PROXYUSE) {
                $aContext = array(
                    'http' => array(
                        'proxy' => 'tcp://' . PROXYNAME . ':' . PROXYPORT,
                        'request_fulluri' => true,
                    ),
                );
                $cxContext = stream_context_create($aContext);
            }
            else
                $cxContext = null;
        
            $citiesInfoResponse = file_get_contents(CITIES_SERVICE . $datasetIdsQueryString, False, $cxContext);
            $citiesInfoObj = json_decode($citiesInfoResponse, true);
       // ($appId, $url, $name, $description, $created, $userId, $cities, $cityNames, $image) {
        for ($i=0; $i<sizeof($citiesInfoObj); $i++) {
            $citiesInfoObj[$i]["id"] =  City::getCityId($citiesInfoObj[$i]["name"], 0, 0);
           } 
        
        

           /* foreach ($app->datasetIds as $datasetId) {

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
            }*/
       // }
       // Database::disconnect();
    }
    
                Util::printJsonObj($citiesInfoObj);
    //Util::printJsonObj($cities);
}

?>