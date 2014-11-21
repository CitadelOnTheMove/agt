<?php
ob_start();
include_once 'Config.php';
include_once CLASSES . 'Database.class.php';
include_once CLASSES.'AppSettingsDefinitions.class.php';
include_once CLASSES.'WriteConfig.class.php';
include_once CLASSES.'DatasetFilenameDefinitions.class.php';
//include_once CLASSES.'ZipUtil.class.php';

$uid = isset($_GET['uid'])?$_GET['uid']:'';
$template = isset($_GET['theme'])?$_GET['theme']:'';
$sql = "SELECT * FROM apps where uid = :uid";
$sqlParams[":uid"] = $uid;

    try {
      Database::connect();
      $sth = Database::$dbh->prepare($sql);
      $sth->execute($sqlParams);
      if ($result = $sth->fetch(PDO::FETCH_ASSOC)) {
        $uid = $result['uid'];
        $name = $result['name'];
        $datasetIds = array();
        $cityIds = array();

        // App found in database, now we load its settings
        $sql = "SELECT * FROM apps_settings WHERE app_uid = :uid";
        try {
          $sth = Database::$dbh->prepare($sql);
          $sth->execute($sqlParams);
          while ($row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {            
            switch($row['apps_settings_definition_id']){
              //case AppSettingsDefinitions::TEMPLATE: $template = $row['value'];break;
              case AppSettingsDefinitions::CITY:  array_push($cityIds, $row['value']);break;
              case AppSettingsDefinitions::DATASET:  array_push($datasetIds, $row['value']);break;
              case AppSettingsDefinitions::COLOR:   $color = $row['value'];break;
              case AppSettingsDefinitions::DARKCOLOR:  $darkColor = $row['value'];break;  
              default: ;//Do nothing
            }           
          }
          //return new Download($uid, $name, $template, $datasetIds, $cityIds, $color, $darkColor);
          
          
        } catch (Exception $e) {
          if (DEBUG)
            $sth->debugDumpParams();
          Util::throwException(__FILE__, __LINE__, __METHOD__, "select app settings failed", $e->getMessage(), $e);
          return false;
        }
      }
      else {
        return false;
      }
    } catch (Exception $e) {
      if (DEBUG)
        $sth->debugDumpParams();
      Util::throwException(__FILE__, __LINE__, __METHOD__, "select app uid failed", $e->getMessage(), $e);
    }

//$template = "Pois";
$fileName = $uid.' template.zip';
switch ($template) {
case "parking": 
    $rootDir = "Citadel-Parkings-Template-master";
    Zip('./download/Citadel-Parkings-Template-master', './'.$uid.' template.zip'); break;
case "poi":
$rootDir = "Citadel-Pois-Template-master";
	Zip('./download/Citadel-Pois-Template-master', './'.$uid.' template.zip'); break;	
case "events":
   $rootDir = "Citadel-Events-Template-master";
	Zip('./download/Citadel-Events-Template-master', './'.$uid.' template.zip');	break;
}

$datasetIdsQueryString = "?format=json&datasetIds=";
 foreach ($datasetIds as $datasetId) {
    $datasetIdsQueryString .= $datasetId . ",";
}
// Removing the trailing comma
$datasetIdsQueryString = rtrim($datasetIdsQueryString, ",");
if (isset($_GET['cityId'])) {
    $cityName = urlencode(  City::getCityName($_GET['cityId']));
    $datasetIdsQueryString = $datasetIdsQueryString . "&city=" . $cityName;
}


// We will call a service that will give us a list of cities, based on the datasetIds
// we provide it with
if (PROXYUSE) {
    $aContext = array(
        'http' => array(
            'proxy' => 'tcp://' . PROXYNAME . ':' . PROXYPORT,
            'request_fulluri' => true,
        ),
    );
        $cxContext = stream_context_create($aContext);
    }
else
    $cxContext = null;
    $datasetsInfoResponse = file_get_contents(DATASETS_SERVICE . $datasetIdsQueryString, False, $cxContext);
    $response = json_decode($datasetsInfoResponse, true);
    $datasetNames = array();
foreach ($response["datasets"] as $dataset) {
    // handle datasets with empty spaces in their filename
   // $json = file_get_contents(str_replace(" ", "%20", $dataset['url']), False, $cxContext);
    $datasetName = str_replace(" ", "%20", basename($dataset['url']));
    $zip = new ZipArchive;
if ($zip->open($fileName) === TRUE) {
    $zip->addFromString($rootDir.'/data/'.$datasetName, file_get_contents(str_replace(" ", "%20", $dataset['url']), False, $cxContext));
    $zip->close();
    $datasetNames[] = 'data/'.$datasetName;
    echo 'added dataset ';
} else {
    echo 'failed adding dataset ';
}
}
write_config($uid, $template, $name, $color, $darkColor, $datasetNames);
if ($zip->open($fileName) === TRUE) {
$zip->addFile("./temp/config ".$uid.".php", $rootDir."/Config.php");
    $zip->close();
    unlink("./temp/config ".$uid.".php");
	echo 'added config ';
    } else {
    echo 'failed';
}

function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
    	echo 'file doesnt exist';
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            //$file = realpath($file);
			
			$linux_source = $rootDir;

            if (is_dir($file) === true )
            {	
				$toadd = str_replace($source . '/', '', $file . '/');
                $zip->addEmptyDir($toadd);
				echo 'toadd dir:  '. $toadd. " <br/> \n";
			
            }
            else if (is_file($file) === true)
            {
				$toadd = str_replace($source . '/', '', $file. '/');
                $zip->addFromString($toadd, file_get_contents($file));
				echo 'toadd:  '. $toadd. " <br/> \n";
				
            }
        }
    }
    else if (is_file($source) === true)
    {
	     $toadd =   basename($source);
		 echo 'toadd base:  '. $toadd. " <br/> \n";
        $zip->addFromString($toadd, file_get_contents($source));
    }
	
    return $zip->close();
}

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header('Content-Disposition: attachment; filename="'.$fileName.'"');
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache"); 
header("Content-Length: " . filesize($fileName));
flush();
ob_clean();

readfile($fileName);
//delete file from server
header("Connection: close");
unlink(HTDOCS_ROOT . BASE_DIR . $fileName);

?>