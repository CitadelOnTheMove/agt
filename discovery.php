<?php

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
include_once CLASSES . 'AppGeneratorException.class.php';

$filters = array();
$keys = array();
$app_datasets = array();

try {
    if (isset($_POST['uid'])) {
        Database::connect();
        if ($app = App::createFromDb($_POST['uid'])) {
            
            $name = $app->name;
            $cityIdParam = "";
            $cityName = "";
            $datasetIdsQueryString = "?format=json&datasetIds=";
			$datasetids = ($_POST['datasetIds']);
             foreach ($datasetids as $id) {
				$datasetId = ($id);
                $datasetIdsQueryString .= $datasetId . ",";
            }
             // Removing the trailing comma
            $datasetIdsQueryString = rtrim($datasetIdsQueryString, ",");
            // We will call a service that will give us a set of datasets, based on the datasetIds
            // we provide it with
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
            $datasetsInfoResponse = file_get_contents(DATASETS_SERVICE . $datasetIdsQueryString, False, $cxContext);
            $response = json_decode($datasetsInfoResponse, true);
            $count = 0;
            foreach ($response["datasets"] as $dataset) {
               if ($count > 1 ) {
                  break;
              }
                if (strpos($dataset['url'],'PropertyAgent') !== false && strpos($dataset['url'], '.json') == true) {
                    $count++;
                $type = $dataset['category'];
                $cityId = $_POST['cityId'];
                // handle datasets with empty spaces in their filename
                $json = file_get_contents(str_replace(" ", "%20", $dataset['url']), False, $cxContext);
                $json = utf8_encode ( $json );
                $assocArray = json_decode($json, true);

                $poisDataset = ResponseDataset::createFromArray(DatasetTypes::Poi, $assocArray);
                $status = 'success';
                $poisCounter = 0;

                foreach ($poisDataset->poi as $poi) {
                    $poi->cityId = $cityId;
                    $filterExists = false;

                    foreach ($poi->category as $cat) {

                        $filterExists = false;
                        $upperCat = ucfirst($cat);

                        if (!empty($filters)) {
                            foreach ($filters as $filter) {
                                if (($filter->cityId == $cityId) && ($filter->name == $upperCat)) {
                                    $filterExists = true;
                                    break;
                                }
                            }
                        }
                        if (!$filterExists) {
                            $filters[] = new Filter($upperCat, false, $type, $cityId, true, $poisCounter);
                        }
                    }

                    foreach ($filters as $filter) {
                        if ($upperCat == $filter->name) {
                            $filter->poisCounter++;
                        }
                    }
                }
                array_push($app_datasets, new ResponseDataset($poisDataset, $name));
            }
            }
            /* sort filters array alphabetically by category name */
            usort($filters, function($a, $b) {
                        return strcmp($a->name, $b->name);
                    });
            /* only the first filter appears to be checked */
            $filters[0]->selected = true;
            Util::printJsonObj(new Response($app_datasets, $name, $filters, $status));
        }
        
        else
        {
            Util::printJsonObj(new ResponseError("failed", "invalid application id", "Application doesn't exist!"));
            Database::disconnect();
        }
    }
    else
        Util::printJsonObj(new ResponseError("failed", "missing application id", "Application doesn't exist!"));
} catch (AppGeneratorException $e) {
    $status = 'failed';
    $error = $e->errorMessageForDevelopper();
    $message = $e->errorMessageForUser();
    Util::printJsonObj(new ResponseError($status, $error, $message));
}
?>