<?php

/**
 * Used for updating the title, the description and the image of an existed app
 * @param $appId the unique identifier of the app
 */

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

    if ($app->image != null) {
        $image = $app->image;
        $sql = "UPDATE apps SET name=:name, description=:description, image=:image WHERE uid=:appId AND apps.isDeleted = 0";
        $useSqlParam = true;
    } else {
        $sql = "UPDATE apps SET name=:name, description=:description WHERE uid=:appId AND apps.isDeleted = 0";
        $useSqlParam = false;
    }

    try {
        $stmt = Database::$dbh->prepare($sql);
        $stmt->bindParam("name", $app->name);
        $stmt->bindParam("description", $app->description);
        if ($useSqlParam)
            $stmt->bindParam("image", $image);
        $stmt->bindParam("appId", $appId);
        $stmt->execute();
        echo "success";
    } catch (PDOException $e) {
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}
Database::disconnect();
?>



