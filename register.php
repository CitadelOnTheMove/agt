<?php
include_once 'Config.php';
require CLASSES . 'init.php';

$userLoggedIn = false;
// Checking if the user is already logged in
if (isset($_SESSION['id'])) {
    $user = $users->userdata($_SESSION['id']);
    $username = $user['username'];
    $userLoggedIn = true;
}
// if form is submitted
if (isset($_POST['submit'])) {

    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {

        $errors[] = '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
      All fields are required.</p>
  </div></div>';
    } else {
        #validating user's input with functions that we will create next
        if ($users->user_exists($_POST['username']) === true) {
            $errors[] = '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
      That username already exists</p>
  </div></div>';
        }
        if (!ctype_alnum($_POST['username'])) {
            $errors[] = '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
    Please enter a username with only alphabets and numbers</p>
  </div></div>';
        }
        if (strlen($_POST['password']) < 6) {
            $errors[] =
                    '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
    Your password must be at least 6 characters long</p>
  </div></div>';
        } else if (strlen($_POST['password']) > 18) {
            $errors[] = '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
    Your password cannot be more than 18 characters long</p>
  </div></div>';
        }
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors[] =
                    '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
      Please enter a valid email address</p>
  </div></div>';
        } else if ($users->email_exists($_POST['email']) === true) {
            $errors[] =
                    '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
    That email already exists.</p>
  </div></div>'
            ;
        }
    }

    if (empty($errors) === true) {
        $username = htmlentities($_POST['username']);
        $password = $_POST['password'];
        $email = htmlentities($_POST['email']);
        $users->register($username, $password, $email);
        header('Location: register.php?success');
        exit();
    }
}

// Registration successful 
if (isset($_GET['success']) && empty($_GET['success'])) {
    header('Location: login.php?success');
    exit();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--------------- CSS files ------------------->    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile.structure-1.2.0.min.css" /> 
        <link rel="stylesheet" href="css/my.css" />  
        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>
        <!-- jQuery Mobile Library -->
        <script src="js/jquery.mobile-1.2.0.min.js"></script>  

    </head>
    <body>	
        <div data-role="page">
            <div data-role="header">
                <h1>Register</h1>
            </div>

            <div id="registrationWrapper"  class="ui-content">
                <?php if ($userLoggedIn) { ?>
                    <h3><?php echo 'You are currently logged in as ' . $username ?>. To register, you first need to <a href="logout.php" data-ajax="false">log out</a></h3>
                <?php } else { ?>
                    <form method="post" action="register.php"  data-ajax="false">
                        <h4>Username:</h4>
                        <input type="text" name="username" />
                        <h4>Password:</h4>
                        <input type="password" name="password" />
                        <h4>Email:</h4>
                        <input type="email" name="email" />	
                        <br>
                        <input type="submit" name="submit" value="Register" />
                    </form>
                    <p>Already have an account? <a href="login.php">Log in now</a></p>

                    <?php
                    # if there are errors, they would be displayed here.
                    if (empty($errors) === false) {
                        echo '<p>' . implode('</p><p>', $errors) . '</p>';
                    }
                }
                ?>
            </div>
    </body>
</html>