<?php


/**
 * For retrieving information needed about user applications
 */
class AppInfo {

    /*
     * Member variables are public in order 
     * to be serialized properly by json_encode
     */
    public $appId;
    public $url;
    public $name;
    public $description;
    public $created;
    public $userId;
    public $cities;
    public $cityNames;
    public $image;
    
    // Added when dataset->city relationship ceased to exist in the AGT database
    public $datasetIds;

    
    /**
     * Creates a new instance of the AppInfo object
     * @param string $appId the unique identifier of the app
     * @param string $url the url of the app
     * @param string $name the name of the app
     * @param string $description the name of the app
     * @param type $created
     * @param string $userId the unique identifier of the user
     * @param type $cities
     * @param type $cityNames
     * @param string $image the image of the app
     */
    public function __construct($appId, $url, $name, $description, $created, $userId, $cities, $cityNames, $image, $datasetIds) {
        $this->appId = $appId;
        $this->url = $url;
        $this->name = $name;
        $this->description = $description;
        $this->created = $created;
        $this->userId = $userId;
        $this->cities = $cities;
        $this->cityNames = $cityNames;
        $this->image = $image;
         $this->datasetIds = $datasetIds;
    }

}