<?php
include_once 'Config.php';
require CLASSES . 'init.php';
$userLoggedIn = false;
// When a user register's sucessfully, he is redirected to the login page to log in
// If this is the case, a "Registration complete" message is displayed to the user.
$fromRegistraton = false;


if (isset($_GET['success']) && empty($_GET['success'])) {
    $fromRegistraton = true;
}

// Checking if the user is already logged in
if (isset($_SESSION['id'])) {
    $user = $users->userdata($_SESSION['id']);
    $username = $user['username'];
    $userLoggedIn = true;
}

if (empty($_POST) === false) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) === true || empty($password) === true) {
        $errors[] = '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
      Sorry, but we need your username and password.</p>
  </div></div>'
        ;
    } else if ($users->user_exists($username) === false) {
        $errors[] = '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
      Sorry that username doesn\'t exist.</p>
  </div></div>';
    } else if ($users->email_confirmed($username) === false) {
        $errors[] = 'Sorry, but you need to activate your account.
    Please check your email.';
    } else {
        $login = $users->login($username, $password);
        if ($login === false) {
            $errors[] = '<div class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
      Sorry, that username/password is invalid</p>
  </div></div>';
        } else {
            // username/password is correct and the login method of the $users object returns the user's id, which is stored in $login.
            $_SESSION['id'] = $login; // The user's id is now set into the user's session in the form of $_SESSION['id']
            header('Location: appForm.php');
            exit();
        }
    }
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
        <title>Login</title>
    </head>
    <body>
        <div data-role="page">
            <div data-role="header">
                <h1>Log in</h1>
            </div>
            <div id="loginWrapper" class="ui-content">
                  <?php if ($fromRegistraton && !$userLoggedIn) { ?>
                    <p>Your registration was <b>successful</b>! Use the form below to log in.</p>
                <?php }?>
                
                <?php if ($userLoggedIn) { ?>
                    <h3><?php echo 'You are currently logged in as ' . $username ?>. To log in as a different user, you first need to <a href="logout.php" data-ajax="false">log out</a></h3>
                <?php } else { ?>
                    <?php
                    if (empty($errors) === false) {

                        echo '<p>' . implode('</p><p>', $errors) . '</p>';
                    }
                    ?>
                    <form method="post" action="login.php" data-ajax="false">
                        <h4>Username:</h4>
                        <input type="text" name="username">
                        <h4>Password:</h4>
                        <input type="password" name="password">
                        <br>
                        <input type="submit" name="submit" value="Log in">
                    </form>

                    <p>Don't have an account? <a href="register.php">Register now</a></p>
                <?php } ?>
            </div>
        </div>
    </body>
</html>