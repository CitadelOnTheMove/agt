<?php

class AppInfo {
     
    	public $url;
	public $name;
	public $created;
        public $username;
	
	
	public function __construct($url, $name, $created, $username) {
                $this->url = $url;
		$this->name = $name;
		$this->created = $created;
                $this->username = $username;
	}
}