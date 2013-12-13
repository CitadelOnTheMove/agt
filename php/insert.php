<?php

include_once '../Config.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'Poi.class.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';

$poiTitle = $_POST['poiTitle'];
$poiDesc = $_POST['poiDesc'];
$poiCategory = array($_POST['poiCategory']);
$poiLat = $_POST['poiLat'];
$poiLng = $_POST['poiLng'];
$poiAddress = $_POST['poiAddress'];
$poiCity = $_POST['poiCity'];
$poiPostal = $_POST['poiPostal'];
$poiPhone = $_POST['poiPhone'];
$poiUrl = $_POST['poiUrl'];


$location = array('point' => array('term' => '',
        'pos' => array('srsName' => '',
            'posList' => $poiLat . " " . $poiLng)),
    'address' => array('postal' => $poiPostal,
        'value' => $poiAddress,
        'city' => $poiCity));

$attribute = array(array('text' => $poiPhone,
        'term' => "Tel",
        'tplIdentifier' => "#Citadel_telephone",
        'type' => "tel"),
    array('text' => $poiUrl,
        'term' => "url",
        'tplIdentifier' => "#Citadel_website",
        'type' => "url"));


$poiArray = array('id' => 'UserGen'.Util::getNextPoiID(), 'title' => $poiTitle, 'description' => $poiDesc,
    'category' => $poiCategory,
    'location' => $location,
    'attribute' => $attribute);


Database::connect();
Database::begin();
$poi = Poi::createFromArray($poiArray);
if ($poi->save(DATASET_ID)) {   
    Database::commit();    
} else {
    Database::rollback();  
}
Database::disconnect();
?>