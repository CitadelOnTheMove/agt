<?php
/*
 * Configuration settings
*/

// directories
define("HTDOCS_ROOT", "C:/wamp/www/");                 //Don't forget to insert the web root directory (for example: 
                                             // "C:/wamp/www/")
define("BASE_DIR", "app-generator/" );
define("CLASSES_DIR", "php/");
define("CLASSES", HTDOCS_ROOT . BASE_DIR . CLASSES_DIR);
//define("SERVERNAME", "http://10.1.1.1/");       //Don't forget to replace localhost
define("SERVERNAME", "http://localhost/");      //with your IP address, if you want to connect to the template with your mobile phone

//appForm: path to store the resized images
define("MY_PATH", "C:\\Users\\Lkallipolitis\\Desktop\\tmp\\");

//Proxy settings
define("PROXYUSE", false); 
define("PROXYNAME", "10.1.1.51"); // without the "http://"
define("PROXYPORT", "8080"); 

// The service running on the main Citadel website
define("CHECKLOGINSERVICE", "http://www.citadelonthemove.eu..."); // remove login checking if not applicable
define("CITADELLOGINLINK","http://www.citadelonthemove.eu....");
define("CITIES_SERVICE", "http://www.citadelonthemove.eu/DesktopModules/DatasetLibrary/API/Service/GetCities");
define("DATASETS_SERVICE", "http://www.citadelonthemove.eu/DesktopModules/DatasetLibrary/API/Service/GetDatasets");
define("DATASETS_PREVIEW_URL","http://citadel.rbox.tv/converter?p_p_id=converter_WAR_converter10SNAPSHOT&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=saveFile_download&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2&_converter_WAR_converter10SNAPSHOT_page_param_download_link={URL}&_converter_WAR_converter10SNAPSHOT_extended_mvc_request_parameter=saveFile");

// database
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_HOSTNAME", "localhost");
define("DB_PORT", "3306");
define("DB_NAME", "citadel_app_generator");

define("DEBUG", true);

// Center of Europe
 
define("MAP_CENTER_LATITUDE", 48.387598);
define("MAP_CENTER_LONGITUDE", 13.723521);
define("MAP_ZOOM", 5);

// dataset
define("DATASET_FILE", HTDOCS_ROOT . BASE_DIR ."data/POI_athens.json");
define("DATASET_ID", 36);
define("DATASET_URL", SERVERNAME . BASE_DIR . "dataset.php");
define("USE_DATABASE", true);
define("MAX_DATASETS_PER_USER", 50);
define("MAX_CITY_DISTANCE_KM", 50);
?>
