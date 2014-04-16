<?php

/**
 *  Returns a list of cities formatted as checkboxes
 * to be used in the app creation form
 */

include_once 'Config.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES . 'Util.class.php';
if
 (isset($_GET['action'])) {
    if ($_GET['action'] == 'getCities') {
        $cityId = $_GET['cityId'];           
        $cityIdSafe = (int) $cityId;
        try {
            Database::connect();
            $sql = "SELECT datasets.identifier, datasets.id, cities.name,cities.id as cID, datasets.url
              FROM  city_datasets
              LEFT JOIN .datasets ON (city_datasets.dataset_id = datasets.id)
              LEFT JOIN cities ON (city_datasets.city_id = cities.id)
              WHERE city_datasets.city_id = " . $cityIdSafe;

            $echoString = "";
            $sth = Database::$dbh->prepare($sql);
            $sth->execute();
            foreach (Database::$dbh->query($sql) as $row) {
                $labelName = $row['identifier']." - <div class='dataset_info'>city: <b>".
                             $row['name']."</b>, url: <a href='".$row['url']."' target='_blank'>".$row['url']."</a></div>";
                $echoString .= '<input class="city'.$row['cID'] .'" type="checkbox"  id="dataset' . $row['id'] . 
                    '"  name="datasetIds[]" value="' . $row['id'] 
                    . '"/> <label for="dataset' . $row['id'] . '">' .$labelName. '</label>';                
            }
            if ($echoString == "") {
                $echoString = "No datasets are available for city <b>" . $cityIdSafe . "</b>";
            }
            
            echo $echoString;
           
        } catch (Exception $e) {
            if (DEBUG)
                $sth->debugDumpParams();
            Util::throwException(__FILE__, __LINE__, __METHOD__, "application creation failed", $e->getMessage(), $e);
            return false;
        }
        Database::disconnect();
    }
}
?>
