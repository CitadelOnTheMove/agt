<?php
include_once 'Config.php';
include_once CLASSES . 'Util.class.php';
include_once CLASSES . 'Filter.class.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Response.class.php';
include_once CLASSES . 'PoisDataset.class.php';
include_once CLASSES . 'App.class.php';

/* Gets the colors defined for the given application
 * 
 */

function printColors($uid) {

    if (!empty($uid)) {
        Database::connect();

        $sql = "SELECT apps_available_colors.value
    FROM apps_settings JOIN apps_available_colors ON apps_settings.value = apps_available_colors.id
         WHERE app_uid = '" . $uid . "'  AND apps_settings.apps_settings_definition_id = '3'";

        $sth = Database::$dbh->query($sql);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $color = $result['value'];

        $sql2 = "SELECT apps_available_darker_colors.value
    FROM apps_settings JOIN apps_available_darker_colors ON apps_settings.value = apps_available_darker_colors.id
         WHERE app_uid = '" . $uid . "'  AND apps_settings.apps_settings_definition_id = '4'";

        $sth2 = Database::$dbh->query($sql2);
        $result2 = $sth2->fetch(PDO::FETCH_ASSOC);
        $darkColor = $result2['value'];

        $colors = array("color" => $color, "darkColor" => $darkColor);

        Database::disconnect();
        return $colors;
    } else {
        $colors = array("color" => "#F8545B", "darkColor" => "#4980AE");
        return $colors;
    }
}

?>