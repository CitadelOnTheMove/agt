<?php

include_once CLASSES . 'DatasetTypes.class.php';
include_once CLASSES . 'App.class.php';

/**
 * Returns a json response with datasets and filters of the given app 
 */

class Response {
    /*
     * Member variables are public in order 
     * to be serialized properly by json_encode
     */

    public $applicationData;
    public $appName;
    public $filters;
    public $status;

    
    /**
     * Creates a new instance of the Response object
     * @param PoisDataset[] $applicationData an array of datasets including pois
     * @param string $appName the name of the app
     * @param Filter[] $filters an array of app filters
     * @param string $status can get two values, either success or failed
     */
    public function __construct($applicationData, $appName, $filters, $status) {
        $this->status = $status;
        $this->applicationData = $applicationData;
        $this->appName = $appName;
        $this->filters = $filters;
    }

}

?>