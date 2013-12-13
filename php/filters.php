<?php

session_start();
include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Filter.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'App.class.php';

/**
 * echoes a javascript array with the filters of the active dataset (from Config.php)
 */
//$uid = 'DAB27DC0-67A8-40DF-907D-B806449874FD';
function printFilters($uid) {
   
    $filters = array();

    $keys = array();
    Database::connect();

    if (App::initialiseFromDb($uid)) {
    $app_datasets = array();
    $name = App::$name;

    foreach (App::$datasets as $key => $value) {

      $sql = "SELECT * FROM datasets WHERE id =" . $value;
      
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
      foreach (Database::$dbh->query($sql) as $row) {
        $type = $row['type'];
        $json = file_get_contents($row['url'], False, $cxContext); 
        $assocArray = json_decode($json, true);
        $poisDataset = Response::createFromArray(DatasetTypes::Poi, $assocArray);
        foreach ($poisDataset->poi as $poi) {
          foreach ($poi->category as $cat) {
            if (!in_array($cat, $keys)) {
              $keys[] = $cat;
              $filters[] = new Filter($cat, true, $type);
            }
          }
        }
        array_push($app_datasets, new Response($poisDataset, $name));
      }
    }
    $_SESSION['app_datasets'] = $app_datasets;
  }

    Database::disconnect();   
    Util::printJsonObj($filters); 
}

?>