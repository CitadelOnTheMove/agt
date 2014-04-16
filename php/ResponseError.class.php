<?php
include_once CLASSES . 'DatasetTypes.class.php';
include_once CLASSES . 'App.class.php';

/** 
 * Returns a json response with errors
 */
class ResponseError {

    public $status;
    public $error;
    public $message;

    
    /**
     * Creates a new instance of the App object
     * @param string $status indicates if the function succeeded or not(in this case is set to "failed") 
     * @param string $error indicates the error
     * @param string $message a message to the user
     */
    public function __construct($status, $error, $message) {
        $this->status = $status;
        $this->error = $error;
        $this->message = $message;
    }
}

?>