<?php
include_once '../Config.php';
include_once CLASSES.'Response.class.php';
include_once CLASSES.'PoisDataset.class.php';
include_once CLASSES.'Util.class.php';

include_once CLASSES.'Database.class.php';

$handle = fopen(DATASET_FILE, "r");
$json = fread($handle, filesize(DATASET_FILE));
fclose($handle);

$assocArray = json_decode($json, true);

// open db connection
Database::connect();
Database::begin();
$dataset = PoisDataset::createFromArray($assocArray['dataset']);
if($dataset->save()) {
	// close db connection
	Database::commit();	
	echo 'Add this DATASET_ID to your Config.php file: ' . $dataset->id;}
else {
	Database::rollback();
	echo 'fail';
}
Database::disconnect();

?>