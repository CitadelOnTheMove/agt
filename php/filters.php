<?php // session_start();
include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Filter.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'ResponseDataset.class.php';
include_once CLASSES . 'App.class.php';

/**
 * echoes a javascript array with the filters of the active dataset (from Config.php)
 */
//$uid = 'FBA62538-BBAF-409A-A7CE-F01A18A2D3BA';
function printFilters($uid) {

    $filters = array();
    //$selectedCities = array();
    $keys = array();
    Database::connect();

    if (App::createFromDb($uid)) {
        $app_datasets = array();
        $name = App::$name;

        foreach (App::$datasets as $value) {

            $sql = "SELECT * FROM datasets WHERE id =" . $value;
            $sqlCity = "SELECT city_id FROM city_datasets WHERE dataset_id=" . $value;

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

                foreach (Database::$dbh->query($sqlCity) as $city) {
                    $cityId = $city['city_id'];

                    $json = file_get_contents($row['url'], False, $cxContext);
                    $assocArray = json_decode($json, true);
                    $poisDataset = ResponseDataset::createFromArray(DatasetTypes::Poi, $assocArray);
                    foreach ($poisDataset->poi as $poi) {
                        foreach ($poi->category as $cat) {

                            if (!in_array($cat, $keys)) {
                                $keys[] = $cat;
                                if ($cat === reset($keys)) {
                                    $filters[0] = new Filter($cat, true, $type, $cityId, true);
                                } else {
                                    $filters[] = new Filter($cat, false, $type, $cityId, true);
                                }
                            }
                        }
                    }
                    array_push($app_datasets, new ResponseDataset($poisDataset, $name));
                }
            }
        }
        $_SESSION['app_datasets'] = new Response($app_datasets, "etststest", $filters);
    }

    Database::disconnect();
    Util::printJsonObj($filters);
}
?>


