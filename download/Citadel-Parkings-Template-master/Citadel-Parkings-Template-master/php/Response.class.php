<?php
include_once CLASSES.'DatasetTypes.class.php';

class Response {
	
	public $dataset;
	
	public function __construct($dataset) {
		$this->dataset = $dataset;
	}
	
	public static function createFromDb($type, $datasetId) {
		if(isset($datasetId)) {
			switch($type) {
				case DatasetTypes::Poi:
					return PoisDataset::createFromDb($datasetId);
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param DatasetTypes $type the type ot this dataset
	 * @param array $assocArray an associative array representation of the struct
	 * @return Ambigous <PoisDataset, boolean>|boolean
	 */
	public static function createFromArray($type, $assocArray) {
		if(isset($assocArray)) {			
			switch($type) {
				case DatasetTypes::Poi:
					return PoisDataset::createFromArray($assocArray['dataset']);
			}
		}
		return false;
	}
}

?>