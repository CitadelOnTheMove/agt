<?php

include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'App.class.php';
include_once CLASSES . 'City.class.php';
include_once CLASSES . 'Dataset.class.php';
include_once CLASSES . 'AppInfo.class.php';

$dashbord = array();
Database::connect();

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    $sql = "SELECT * from apps 
        WHERE apps.userId = :userId AND apps.isDeleted = 0 ORDER BY apps.created DESC";
    $useSqlParams = true;

} else {

    $sql = "SELECT * from apps 
        WHERE apps.isDeleted = 0";
    $useSqlParams = false;
}

try {
    $sth = Database::$dbh->prepare($sql);
    if ($useSqlParams) {
        $sqlParams[":userId"] = $userId;
        $sth->execute($sqlParams);
    }
    else{
         $sth->execute();
    }
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $appId = $row['uid'];
        $appName = $row['name'];
        $description = $row['description'];
        $created = $row['created'];
        $userId = $row['userId'];

        $appCitiesSQL = "SELECT * FROM apps_settings 
             JOIN cities on apps_settings.value = cities.id WHERE apps_settings.app_uid =:appId" .
                " AND apps_settings_definition_id = " . AppSettingsDefinitions::CITY;

        $sqlParams2[":appId"] = $appId;
        $sth2 = Database::$dbh->prepare($appCitiesSQL);
        $sth2->execute($sqlParams2);
        $results2 = $sth2->fetchAll(PDO::FETCH_ASSOC);

        $cities = array();
        foreach ($results2 as $row) {
            $cityId = $row['id'];
            $cityName = $row['name'];
            $latitude = $row['latitude'];
            $longitude = $row['longitude'];

            $citiesDatasetsSql = "SELECT * FROM city_datasets Inner Join datasets On 
                city_datasets.dataset_id =datasets.id Inner Join cities On city_datasets.city_id = cities.id 
                WHERE city_datasets.city_id = :cityId";

            $sqlParams3[":cityId"] = $cityId;
            $sth3 = Database::$dbh->prepare($citiesDatasetsSql);
            $sth3->execute($sqlParams3);
            $results3 = $sth3->fetchAll(PDO::FETCH_ASSOC);

            $datasets = array();
            foreach ($results3 as $row) {
                $dataset = new Dataset($row['id'], $row['identifier'], null, null, null, null, null, null, null, $row['url']);
                array_push($datasets, $dataset);
            }

            $city = new City($cityId, $cityName, $latitude, $longitude, $datasets);
            array_push($cities, $city);
        }

        $appUrl = SERVERNAME . BASE_DIR . "index.php" . '?uid=' . $appId;

        $dashbord[] = new AppInfo($appId, $appUrl, $appName, $description, $created, $userId, $cities);
    }
} catch (Exception $e) {
    if (DEBUG)
        $sth->debugDumpParams();
    Util::throwException(__FILE__, __LINE__, __METHOD__, "query database failed", $e->getMessage(), $e);
    return false;
}
Database::disconnect();

$arr = array('apps' => $dashbord);
Util::printJsonObj($arr);
?>



