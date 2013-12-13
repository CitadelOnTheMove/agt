<?php

include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'City.class.php';


/**
 * echoes a javascript array with the filters of the active dataset (from Config.php)
 */
//function printCities() {
$cities = array();

Database::connect();


$sql1 = 'SELECT * FROM cities ORDER BY name';

foreach (Database::$dbh->query($sql1) as $row) {
    $name = $row['name'];
    $lat = $row['latitude'];
    $lon = $row['longitude'];

    $cities[] = new City($name, $lat, $lon);
}

Database::disconnect();

Util::printJsonObj($cities);
?>