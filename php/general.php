<?php

/**
 * Handles user authentication sessions
 */
class General {

    /**
     * Check if the user is logged in.
     * @return true on success of false otherwise
     */
    public function logged_in() {
        return(isset($_SESSION['id'])) ? true : false;
    }

    /**
     * Check if logged in then redirect to home.php
     */
    public function logged_in_protect() {
        if ($this->logged_in() === true) {
            header('Location: home.php');
            exit();
        }
    }

    /**
     * Check if not logged in then redirect to index.php 
     */
    public function logged_out_protect() {
        if ($this->logged_in() === false) {
            header('Location: login.php');
            exit();
        }
    }

}

?>
