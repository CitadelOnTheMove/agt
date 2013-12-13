<?php

include_once CLASSES . 'DatasetTypes.class.php';
include_once CLASSES . 'App.class.php';

class Response {

    public $dataset;
    public $appName;
    
       public function __construct($dataset, $appName) {
        $this->dataset = $dataset;
         $this->appName = $appName;
     
    }

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
        //$appName = App::$name;
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