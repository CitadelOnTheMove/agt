<?php
session_start();
header('Content-type: application/json; charset=utf-8');
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include_once 'Config.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'ResponseDataset.class.php';
include_once CLASSES . 'ResponseError.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'App.class.php';
include_once CLASSES . 'Filter.class.php';
include_once CLASSES . 'Database.class.php';

$filters = array();

$keys = array();
if (isset($_GET['uid'])) {
  Database::connect();

  if ($app = App::createFromDb($_GET['uid'])) {
    $app_datasets = array();
    $name = $app->name;

    foreach ($app->datasetIds as $key => $value) {

      $sql = "SELECT * FROM datasets WHERE id =" . $value;

      /* If we are behind a proxy, we need to
       * setup a context for file_get_contents
       */
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
        $poisDataset = ResponseDataset::createFromArray(DatasetTypes::Poi, $assocArray);
        foreach ($poisDataset->poi as $poi) {
          foreach ($poi->category as $cat) {
            if (!in_array($cat, $keys)) {
              $keys[] = $cat;
              $filters[] = new Filter($cat, true, $type);
            }
          }
        }
        array_push($app_datasets, new ResponseDataset($poisDataset, $name));
      }
    }    
    Util::printJsonObj(new Response($app_datasets, $name, $filters));
  }
  else
    Util::printJsonObj(new ResponseError("failed", "invalid application id"));
  Database::disconnect();
}
else
  Util::printJsonObj(new ResponseError("failed", "missing application id"));
?>