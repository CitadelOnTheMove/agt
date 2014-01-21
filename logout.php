<?php

session_start();
session_destroy();
header('Location: appForm.php');
exit();
?>