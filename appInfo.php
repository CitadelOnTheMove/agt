<?php

include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'App.class.php';
include_once CLASSES . 'AppInfo.class.php';

$dashbord = array();
Database::connect();

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $sql = "SELECT apps.uid, apps.name, apps.created, users.username from apps 
         LEFT JOIN users On users.id = apps.userId WHERE apps.userId = :userId";
} else {
    $username = "";
    $sql = "SELECT apps.uid, apps.name, apps.created, users.username from apps 
        LEFT JOIN users On users.id = apps.userId";
}

try {
    $sqlParams[":userId"] = $userId;
    $sth = Database::$dbh->prepare($sql);
    $sth->execute($sqlParams);
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $appId = $row['uid'];
        $appName = $row['name'];
        $created = $row['created'];
        $username = $row['username'];
        
        $appCitiesSQL = "SELECT cities.name FROM apps_settings 
             JOIN cities on apps_settings.value = cities.id WHERE apps_settings.app_uid =:appId".
            " AND apps_settings_definition_id = ".AppSettingsDefinitions::CITY;
        
        $sqlParams2[":appId"] = $appId;
        $sth2 = Database::$dbh->prepare($appCitiesSQL);
        $sth2->execute($sqlParams2);
        $results2 = $sth2->fetchAll(PDO::FETCH_ASSOC);
    
        $cities = array();
        foreach ($results2 as $row) {
          array_push($cities,$row['name']);
        }

        $appUrl = SERVERNAME . BASE_DIR . "index.php" . '?uid=' . $appId;

        $dashbord[] = new AppInfo($appUrl, $appName, $created, $username,$cities);       
        
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



