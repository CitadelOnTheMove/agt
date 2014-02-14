<?php

class AppInfo {     
  public $url;
	public $name;
	public $created;
  public $username;
  public $cities;
	
	public function __construct($url, $name, $created, $username, $cities) {
    $this->url = $url;
		$this->name = $name;
		$this->created = $created;
    $this->username = $username;
    $this->cities = $cities;
	}
}