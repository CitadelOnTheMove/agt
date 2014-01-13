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
$sql = "SELECT apps.uid, apps.name, apps.created, users.username from apps 
       LEFT JOIN users On users.id = apps.userId";

$sth = Database::$dbh->query($sql);
$results = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    $appId = $row['uid'];
    $appName = $row['name'];
    $created = $row['created'];
    $username = $row['username'];

    $appUrl = SERVERNAME . BASE_DIR . "index.php" . '?uid=' . $appId;

    $dashbord[] = new AppInfo($appUrl, $appName, $created, $username);
}
Database::disconnect();

Util::printJsonObj($dashbord);
?>



