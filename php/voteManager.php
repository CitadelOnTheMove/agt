<?php

include_once '../Config.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'Poi.class.php';
include_once CLASSES . 'Vote.class.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';


$poiId = $_GET['poiId'];
if (isset($poiId)) {
    Database::connect();
    Database::begin();
    $result = Vote::getPoiVotes($poiId);

    echo json_encode($result); //[0][0] ."*". $result[1][0];
    Database::disconnect();
} else {

    $poiId = $_POST['poiId'];
    $value = $_POST['poiVote'];
    $voteDate = date("Y-m-d H:i:s");
    Database::connect();
    Database::begin();
    $vote = new Vote($poiId, $voteDate, $value);
    if ($vote->save()) {
        Database::commit();
    } else {
        Database::rollback();
    }
    Database::disconnect();
}
?>