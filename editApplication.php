<?php

include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'App.class.php';


if (isset($_GET['appId'])) {
    $appId = $_GET['appId'];

    $body = @file_get_contents('php://input');
    $app = json_decode($body);

    Database::connect();

    $sql = "UPDATE apps SET name=:name, description=:description WHERE uid=:appId";
    try {

        $stmt = Database::$dbh->prepare($sql);
        $stmt->bindParam("name", $app->name);
        $stmt->bindParam("description", $app->description);
        $stmt->bindParam("appId", $appId);
        $stmt->execute();
        echo "success";
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}
Database::disconnect();
?>



