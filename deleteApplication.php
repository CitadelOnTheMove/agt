<?php

include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'App.class.php';


if (isset($_GET['appId'])) {
    $appId = $_GET['appId'];

    Database::connect();

    $sql = "UPDATE apps SET isDeleted=1 WHERE uid=:appId";
    try {
        $stmt = Database::$dbh->prepare($sql);
        $stmt->bindParam("appId", $appId);
        $stmt->execute();

        echo "The app was deleted successfully";
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}
Database::disconnect();
?>



