<?php

class General {

  //Check if the user is logged in.
  public function logged_in() {
    return(isset($_SESSION['id'])) ? true : false;
  }

  //if logged in then redirect to home.php
  public function logged_in_protect() {
    if ($this->logged_in() === true) {
      header('Location: home.php');
      exit();
    }
  }

  //if not logged in then redirect to index.php
  public function logged_out_protect() {
    if ($this->logged_in() === false) {
      header('Location: login.php');
      exit();
    }
  }
}
?>
