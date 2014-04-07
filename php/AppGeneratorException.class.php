<?php

/**
 * This class is to handle the error exceptions 
 */
class AppGeneratorException extends Exception {

    /*returns an error message that only developpers can see, 
     * saying in which file and at which line the error occurs
     */
    public function errorMessageForDevelopper() {
        $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile();
        return $errorMsg;
    }

    /*returns an error message to inform the user 
     */
    public function errorMessageForUser() {
        return $this->getMessage();
    }

}

?>
