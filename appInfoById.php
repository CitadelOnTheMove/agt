<?php

/**
 * Fetches details of the given app
 * @return json AppInfo object
 */
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


if (isset($_GET['appId'])) {
    $appId = $_GET['appId'];

    Database::connect();

    $sql = "SELECT * from apps 
        WHERE apps.uid = :appId AND apps.isDeleted = 0";

    $useSqlParams = true;
}
try {
    $sth = Database::$dbh->prepare($sql);
    if ($useSqlParams) {
        $sqlParams[":appId"] = $appId;
        $sth->execute($sqlParams);
    } else {
        $sth->execute();
    }
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);



    $appDatasetsIds = array();

    foreach ($results as $row) {
        $appId = $row['uid'];
        $appName = $row['name'];
        $description = $row['description'];
        $created = $row['created'];
        $userId = $row['userId'];
        $image = $row['image'];

        //sql for fetching the datasets
        $appDatasetsSql = "SELECT * FROM apps_settings 
             JOIN datasets on apps_settings.value = datasets.id WHERE apps_settings.app_uid =:appId" .
                " AND apps_settings_definition_id = " . AppSettingsDefinitions::DATASET;

        $sqlParamsdataset[":appId"] = $appId;
        $sthDataset = Database::$dbh->prepare($appDatasetsSql);
        $sthDataset->execute($sqlParamsdataset);
        $resultsDataset = $sthDataset->fetchAll(PDO::FETCH_ASSOC);


        foreach ($resultsDataset as $datasetRow) {
            $appDatasetId = $datasetRow['id'];
            array_push($appDatasetsIds, $appDatasetId);
        }


        $categoryNames = "";
        //sql for fetching the categories
        $appCategoriesSql = "SELECT apps_settings.value FROM apps_settings 
            WHERE apps_settings.app_uid =:appId" .
                " AND apps_settings_definition_id = " . AppSettingsDefinitions::CATEGORY;


        $sqlParamCat[":appId"] = $appId;
        $sthCat = Database::$dbh->prepare($appCategoriesSql);
        $sthCat->execute($sqlParamCat);
        $resultsCategories = $sthCat->fetchAll(PDO::FETCH_ASSOC);


        foreach ($resultsCategories as $categoryRow) {
            $categoryName = $categoryRow['value'];
            $categoryNames .= $categoryName . ",";
        }


        //sql for fetching the cities
        $appCitiesSQL = "SELECT * FROM apps_settings 
             JOIN cities on apps_settings.value = cities.id WHERE apps_settings.app_uid =:appId" .
                " AND apps_settings_definition_id = " . AppSettingsDefinitions::CITY;

        $sqlParams2[":appId"] = $appId;
        $sth2 = Database::$dbh->prepare($appCitiesSQL);
        $sth2->execute($sqlParams2);
        $results2 = $sth2->fetchAll(PDO::FETCH_ASSOC);

        $cities = array();
        $cityNames = "";

        foreach ($results2 as $row) {
            $cityId = $row['id'];
            $cityName = $row['name'];
            $cityNames .= $cityName . ",";
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
                if (in_array($row['dataset_id'], $appDatasetsIds)) {
                    $dataset = new Dataset($row['id'], $row['identifier'], null, null, null, null, null, null, null, $row['url']);
                    array_push($datasets, $dataset);
                }
            }

            $city = new City($cityId, $cityName, $latitude, $longitude, $datasets);
            array_push($cities, $city);
        }

        /* Remove the trailing ',' from the string */
        $cityNames = rtrim($cityNames, ",");
        $categoryNames = rtrim($categoryNames, ",");

        $appUrl = SERVERNAME . BASE_DIR . "index.php" . '?uid=' . $appId;

        $dashbord[] = new AppInfo($appId, $appUrl, $appName, $description, $created, $userId, $cities, $cityNames, $image, $appDatasetsIds, $categoryNames);
    }
} catch (Exception $e) {
    if (DEBUG)
        $sth->debugDumpParams();
    Util::throwException(__FILE__, __LINE__, __METHOD__, "query database failed", $e->getMessage(), $e);
    return false;
}

Database::disconnect();

$arr = array('app' => $dashbord);
Util::printJsonObj($arr);
//}
?>



