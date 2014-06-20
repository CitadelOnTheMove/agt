<?php
include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Filter.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'App.class.php';
 
/**
 * Gets the colors defined for the given application
 * @param $uid the unique identifier of the app
 * @return string[] an array with the color value and the dark color value
 */

function printColors($uid) {

    if (!empty($uid)) {
        Database::connect();

        $sql = "SELECT apps_settings.value FROM apps_settings 
         WHERE app_uid = '" . $uid . "'  AND apps_settings.apps_settings_definition_id = '3'";        

        $sth = Database::$dbh->query($sql);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $color = $result['value'];

        $sql2 = "SELECT apps_settings.value FROM apps_settings 
         WHERE app_uid = '" . $uid . "'  AND apps_settings.apps_settings_definition_id = '4'";

        $sth2 = Database::$dbh->query($sql2);
        $result2 = $sth2->fetch(PDO::FETCH_ASSOC);
        $darkColor = $result2['value'];

        $colors = array("color" => $color, "darkColor" => $darkColor);

        Database::disconnect();
        return $colors;
    } else {
        $colors = array("color" => "#E36C0A", "darkColor" => "#13235B");
        return $colors;
    }
}

?>