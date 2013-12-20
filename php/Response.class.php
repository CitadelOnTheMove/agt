<?php
include_once CLASSES . 'DatasetTypes.class.php';
include_once CLASSES . 'App.class.php';

/* Returns a json response with datasets and filters of the given app*/
class Response {

    public $status;
    public $applicationData;
    public $appName;
    public $filters;
    
       public function __construct($applicationData, $appName,$filters,$status="success") {
         $this->status = $status;
         $this->applicationData = $applicationData;
         $this->appName = $appName;
         $this->filters = $filters;     
    }  
}

?>