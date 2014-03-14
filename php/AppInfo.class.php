<?php

class AppInfo {

    public $appId;
    public $url;
    public $name;
    public $description;
    public $created;
    public $userId;
    public $cities;

    public function __construct($appId, $url, $name, $description, $created, $userId, $cities) {
        $this->appId = $appId;
        $this->url = $url;
        $this->name = $name;
        $this->description = $description;
        $this->created = $created;
        $this->userId = $userId;
        $this->cities = $cities;
    }

}