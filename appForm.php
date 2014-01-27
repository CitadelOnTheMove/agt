<?php
include_once 'Config.php';
require CLASSES . 'init.php';
//$general->logged_out_protect();

/* Read user info from session in case login comes from
 * citadel website
 */
if(isset($_SESSION['username']))
{ 
  $userId = $_SESSION['id'];
  $username = $_SESSION['username'];
}
else // use built-in login functionality
{
  $user = $users->userdata($_SESSION['id']);
  $userId = $user['id'];
  $username = $user['username'];
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <!--------------- Metatags ------------------->   
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--------------- CSS files ------------------->    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile.structure-1.2.0.min.css" /> 
        <link rel="stylesheet" href="css/my.css" />  
        <link rel="stylesheet" href="css/spectrum.css" />

        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>
        <!-- jQuery Mobile Library -->
        <script src="js/jquery.mobile-1.2.0.min.js"></script>  

        <script src="js/spectrum.js"></script> 

        <script lang="text/javascript">
            $(document).ready(function() {

                var counter = 0;
                var counterMax = 0;

                $('.ui-checkbox a').bind("click", function(event, data) {
                    event.stopPropagation();
                    window.open($this.attr('href'), $this.attr('target'));
                });

                $("input[type=checkbox][id^=city]").click(function() {

                    /* The id attribute is of the form "cityXX"
                     * where XX is the id of the city
                     */
                    var cityId = $(this).attr('id').substring(4);

                    if ($(this).attr('checked')) {

                        /* Check if we allready have the datasets from previous 
                         * calls and if yes show them.
                         */
                        if ($('input[type=checkbox][name="datasetIds[]"].city' + cityId).length)
                        {
                            $('input[type=checkbox][name="datasetIds[]"].city' + cityId).parent().show();
                        }
                        else {
                            $.ajax({
                                type: "GET",
                                url: "ajaxHelper.php?action=getCities&cityId=" + cityId,
                                cache: false,
                                success: onSuccess,
                                error: onFailure
                            });
                        }
                    } // if 
                    else {
                        /*city checkbox unchecked, hide and uncheck the checkboxes */
                        $('input[type=checkbox][name="datasetIds[]"].city' + cityId).parent().hide();
                        $('input[type=checkbox][name="datasetIds[]"].city' + cityId).attr("checked", false).checkboxradio("refresh");
                    }
                }); // click

            }); // document.ready

            function onSuccess(data, status)
            {
                /* Add the new datasets to the existing ones */
                //var currentContent = $('#datasetsCheckboxes').html();
                $('#datasetsCheckboxes').append(data);

                /* Apply the checkbox styling to the new ones */
                $('input[type=checkbox][name="datasetIds[]"]').each(function() {

                    if ($(this).parent().not('.ui-checkbox').length)
                        $(this).checkboxradio().trigger('create');
                });

                $("input[type=checkbox][id^=city]").checkboxradio('enable');
            }

            function onFailure(data, status)
            {
                //  $('#loadingCities').hide();
                $('#datasetsCheckboxes').html("Dataset retrieval failed");
            }
        </script>

    </head>
    <body>
        <div data-role="page">
            <div data-role="header">
                <h1>Create your app</h1>
                <div class="beta"></div>
                <a href="http://www.citadelonthemove.eu/en-us/innovate/templateapps.aspx" id="documentsLink" data-icon="documents" data-iconpos="left" data-theme="c" title="Documentation" rel="external" class="ui-btn-left">Resources</a> 
                <a href="logout.php" id="logoutLink" data-icon="logout" data-iconpos="left" data-theme="c" title="Log out" class="ui-btn-right">Log out</a> 
            </div>

            <div data-role="content"> 

                <div id="createNewAppWrapper">
                    <?php
                    include_once 'Config.php';
                    include_once CLASSES . 'Database.class.php';
                    include_once CLASSES . 'App.class.php';

                    Database::connect();

                    // define variables and set to empty values
                    $darkColorErr = $colorErr = $nameErr = $cityIdsErr = $datasetIdsErr = "";

                    $error = false;
                    // Check all required fields
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                        if (empty($_POST["cityIds"])) {
                            $cityIdsErr = "City is required";
                            $error = true;
                        }
                        if (empty($_POST["datasetIds"])) {
                            $datasetIdsErr = "Dataset is required";
                            $error = true;
                        }
                        if (empty($_POST["name"])) {
                            $nameErr = "Application Name is required";
                            $error = true;
                        }
                        if (empty($_POST["color"])) {
                            $colorErr = "Color is required";
                            $error = true;
                        }
                        if (empty($_POST["darkColor"])) {
                            $darkColorErr = "Dark Color is required";
                            $error = true;
                        }
                       

                        if (!$error) {
                            if ((isset($_POST["cityIds"])) && (isset($_POST["name"])) &&
                                    (isset($_POST["datasetIds"])) && (isset($_POST["color"])) &&
                                    (isset($_POST["darkColor"]))) {

                                Database::begin();
                                $newApp = App::createFromArray($_POST, $userId);
                                if ($newApp->save()) {
                                    Database::commit();
                                    echo '<div class="success">Your application was created successfully!';
                                    echo '<br><br>';
                                    echo ' <a href="index.php?uid=' . $newApp->uid . '" target="_blank" rel="external">See my app!</a>';
                                    echo ' <a style="float:right" href="appForm.php" target="_blank">Create a new app</a></div>';
                                } else {
                                    Database::rollback();
                                    echo '<div class="failure">Your application was not created.';
                                    echo '<br><br>';
                                    echo ' <a style="float:right" href="appForm.php" target="_blank">Create a new app</a></div>';
                                }
                            }
                        }
                    }

                    function clear_input($data) {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }
                    ?>

                    <?php if ($_SERVER["REQUEST_METHOD"] != "POST" || $error) { ?>
                        <form id="createNewAppForm" data-ajax="false" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            
                          <p>
                            <?php if(!$general->logged_in()){?>
                          <div class="warning">
                              <a target="_blank" href="<?php echo CITADELLOGINLINK; ?>" relation="external">You have to login before creating an app!</a>
                            </div>
                                <?php } else { 
                            echo 'Hi <b>' . $username . '</b>! Use this form to create your own app.';
                             echo '<a style="float:right" href="logout.php" data-ajax="false">log out</a>';
                                } 
                            ?>
                          </p>                            
                            
                            <p><span class="error">* required field.</span></p>                        

                            <legend><b>Select cities:</b> <span class="error">* <?php echo $cityIdsErr; ?></span></legend><br/>
                            <div id="citiesCheckboxes"  data-role="controlgroup">

                                <?php
                                $sql1 = 'SELECT * FROM cities ORDER BY name';
                                foreach (Database::$dbh->query($sql1) as $row) {
                                    echo '<input type="checkbox" autocomplete="off" name="cityIds[]" id="city' . $row['id'] . '"  value="' . $row['id'] . '">
                            <label for="city' . $row['id'] . '">' . $row['name'] . '</label>';
                                }
                                ?>
                            </div>

                            <br><br>

                            <legend><b>Select datasets:</b> <span class="error">* <?php echo $datasetIdsErr; ?></span> </legend><br/>

                            <div id="datasetsCheckboxes" >
                                <?php
                                echo '<i>Select a City to see the available datasets.</i>'
                                ?>
                            </div>

                            <br>
                            <a rel="external" href="/<?php echo BASE_DIR ?>importDatasets.php">Click here if you want to import a new dataset</a>
                            <br><br>

                            <legend><b>Select the basic color of your app:</b><span class="error">* <?php echo $colorErr; ?></span></legend><br/>
                            <input type="color" class="full" name="color">

                            <br><br>

                            <legend><b>Select the secondary color (active/hovered buttons):</b><span class="error">* <?php echo $darkColorErr; ?></span></legend><br/>

                            <input type="color" class="full" name="darkColor">
                            <br><br>

                            <legend><b>Application Name:</b> <span class="error">* <?php echo $nameErr; ?></span></legend><br/>
                            <input type="text" name="name" required>                        
                            
                            <br/><br/>
                            <?php if(!$general->logged_in()){?>
                              <a target="_blank" href="<?php echo CITADELLOGINLINK; ?>" relation="external">You have to login before creating an app!</a>
                            <?php } 
                             else { ?>
                              <input type="submit" name="submit" value="Create the app">
                            <?php }?>  
                        </form>

                    <?php
                    } // end if 
                    Database::disconnect();
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>




