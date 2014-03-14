<?php

class AppGeneratorException extends Exception {

    public function errorMessageForDevelopper() {
        $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile();
        return $errorMsg;
    }

    public function errorMessageForUser() {
        return $this->getMessage();
    }

}

?>
