<?php

// Helper class to write Config.php files for downloaded templates
include_once '../Config.php';

function write_config($uid, $template, $appName, $color, $darkColor, $datasetNames) {
//switch($template) {
//	case "parking": $filename = './download/Citadel-Parkings-Template-master/Citadel-Parkings-Template-master/Config.php'; break;
//	case "poi": $filename = './download/Citadel-Pois-Template-master/Citadel-Pois-Template-master/Config.php'; break;
//	case "events": $filename = './download/Citadel-Events-Template-master/Citadel-Events-Template-master/Config.php'; break;
//}
$filename = './temp/config '.$uid.'.php';
$somecontent =
"<?php\n
/*\n
 * Configuration settings\n
*/\n
\n
// directories\n
define(\"HTDOCS_ROOT\", \"C:/wamp/www/\");                 //Don't forget to insert the web root directory (for example: \n
                                             // \"C:/wamp/www/\")\n";
if ($template === "poi") {
$somecontent .= "define(\"BASE_DIR\", \"Citadel-Pois-Template-master/\" );\n";
}
elseif ($template === "events") {
   $somecontent .= "define(\"BASE_DIR\", \"Citadel-Events-Template-master/\" );\n";
    
}
elseif ($template === "parking") {
    $somecontent .= "define(\"BASE_DIR\", \"Citadel-Parkings-Template-master/\" );\n";
}
    $somecontent.=
"define(\"CLASSES_DIR\", \"php/\");\n
define(\"CLASSES\", HTDOCS_ROOT . BASE_DIR . CLASSES_DIR);\n
//define(\"SERVERNAME\", \"http://10.1.1.1/\");       //Don't forget to replace localhost\n
define(\"SERVERNAME\", \"http://localhost/\");      //with your IP address, if you want to connect to the template with your mobile phone\n
\n
//Proxy settings\n
define(\"PROXYUSE\", false); \n
define(\"PROXYNAME\", \"10.1.1.51\"); // without the \"http://\"\n
define(\"PROXYPORT\", \"8080\"); \n
\n
// database\n
define(\"DB_USERNAME\", \"root\");\n
define(\"DB_PASSWORD\", \"\");\n
define(\"DB_HOSTNAME\", \"localhost\");\n
define(\"DB_PORT\", \"3306\");\n
define(\"DB_NAME\", \"citadel_app_generator\");\n
\n
define(\"DEBUG\", true);\n
\n
// app settings\n
define(\"APP_NAME\", \"".$appName."\");
define(\"APP_COLOR\", \"".$color."\");
define(\"APP_DARKCOLOR\", \"".$darkColor."\");
// views\n
 \n
define(\"MAP_CENTER_LATITUDE\", 37.957243);\n
define(\"MAP_CENTER_LONGITUDE\", 23.721521);\n
define(\"MAP_ZOOM\", 2);\n
\n
// dataset\n
define(\"DATASET_FILE\", serialize (array (";
$count = 0;
foreach ($datasetNames as $name) {
if ($count < 1) {	
$somecontent .= "HTDOCS_ROOT . BASE_DIR . \"" . $name . '"';
}
else {
$somecontent .=	", HTDOCS_ROOT . BASE_DIR . \"" . $name . '"';
}
	$count++;
}
$somecontent .= ")));\n
define(\"DATASET_ID\", 36);\n
define(\"DATASET_URL\", SERVERNAME . BASE_DIR . \"dataset.php\");\n
define(\"USE_DATABASE\", false);\n
\n
?>";

// Let's make sure the file exists and is writable first.

    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.
    if (!$handle = fopen($filename, 'w')) {
         echo "Cannot open file ($filename)";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }

    echo "Success, wrote ($somecontent) to file ($filename)";

    fclose($handle);

} 

?>