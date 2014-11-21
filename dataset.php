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
    if (isset($_GET['uid'])) {
        Database::connect();

        if ($app = App::createFromDb($_GET['uid'])) {
            
            $name = $app->name;
            $cityIdParam = "";
            $cityName = "";

            
            $datasetIdsQueryString = "?format=json&datasetIds=";
             foreach ($app->datasetIds as $datasetId) {
                $datasetIdsQueryString .= $datasetId . ",";
            }
             // Removing the trailing comma
            $datasetIdsQueryString = rtrim($datasetIdsQueryString, ",");
            if (isset($_GET['cityId'])) {
             $cityName = urlencode(  City::getCityName($_GET['cityId']));
             $datasetIdsQueryString = $datasetIdsQueryString . "&city=" . $cityName;
            }


            // We will call a service that will give us a list of cities, based on the datasetIds
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
            foreach ($response["datasets"] as $dataset) {


                $type = $dataset['category'];
                $cityId = $_GET['cityId'];
                // handle datasets with empty spaces in their filename
                $json = file_get_contents(str_replace(" ", "%20", $dataset['url']), False, $cxContext);
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
//                                if (count($filters) == 1) {
//                                   // $filters[0]->selected = true;
//                                }
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
            /* sort filters array alphabetically by category name */
            usort($filters, function($a, $b) {
                        return strcmp(strtolower(ltrim($a->name)), strtolower(ltrim($b->namstrcmpe)));
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
   else if(isset( $_GET['preview']) && isset($_GET['converterdatasetID']))
        {
            
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
            
                // handle datasets with empty spaces in their filename
               //  $json = file_get_contents(str_replace(" ", "%20", "http://www.citadelonthemove.eu/Portals/0/PropertyAgent/517/Files/14/museums-galleries.json"), False, $cxContext);
            //$jsonUrl =   DATASETS_PREVIEW_URL  
            $json = file_get_contents(str_replace("{URL}", $_GET['converterdatasetID'], "http://citadel.rbox.tv/converter?p_p_id=converter_WAR_converter10SNAPSHOT&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=saveFile_download&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&_converter_WAR_converter10SNAPSHOT_page_param_download_link={URL}&_converter_WAR_converter10SNAPSHOT_extended_mvc_request_parameter=saveFile"), False, $cxContext);
                
                $assocArray = json_decode($json, true);
                $status = "success";
                $poisDataset = ResponseDataset::createFromArray(DatasetTypes::Poi, $assocArray);
                array_push($app_datasets, new ResponseDataset($poisDataset, "Test change me"));
                Util::printJsonObj(new Response($app_datasets,"Dataset Preview", $filters, $status));

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