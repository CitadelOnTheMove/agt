<?php

include_once CLASSES . 'DatasetTypes.class.php';
include_once CLASSES . 'App.class.php';

/* Returns a json response with datasets of the given app */
class ResponseDataset {

    public $dataset;

    /**
     * Creates a new instance of the ResponseDataset object
     * @param Dataset $dataset the app dataset
     */
    public function __construct($dataset) {
        $this->dataset = $dataset;       
    }

    /**
     * Fetches PoisDataset instance from database based on the dataset id
     * @param string $type gets the value DatasetTypes::Poi
     * @param int $datasetId the id of the dataset
     * @return PoisDataset|boolean a PoisDataset object or false if not found
     */
    public static function createFromDb($type, $datasetId) {
        if (isset($datasetId)) {
            switch ($type) {
                case DatasetTypes::Poi:
                    return PoisDataset::createFromDb($datasetId);
            }
        }
        return false;
    }

    public static function createFromDb2($type, $datasetIds) {
        if (isset($datasetIds)) {
            switch ($type) {
                case DatasetTypes::Poi:
                    return PoisDataset::createFromDb2($datasetIds);
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
        if (isset($assocArray)) {
            switch ($type) {
                case DatasetTypes::Poi:
                    return PoisDataset::createFromArray($assocArray['dataset']);
            }
        }
        return false;
    }
}

?>