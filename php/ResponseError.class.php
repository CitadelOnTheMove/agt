<?php
include_once CLASSES . 'DatasetTypes.class.php';
include_once CLASSES . 'App.class.php';

/* Returns a json response with datasets and filters of the given app*/
class ResponseError {

    public $status;
    public $error;
    public $message;

    public function __construct($status, $error, $message) {
        $this->status = $status;
        $this->error = $error;
        $this->message = $message;
    }
}

?>