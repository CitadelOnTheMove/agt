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

$userDatasetUploadLimitReached = false;
?>
<!DOCTYPE HTML>
<html>
    <head>
        <!--  Metatags -->  
        <title>Import Dataset</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSS files -->    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile.structure-1.2.0.min.css" /> 
        <link rel="stylesheet" href="css/my.css" />   
        <!-- Google Maps JavaScript API v3 --> 
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>
        <!-- jQuery Mobile Library -->
        <script src="js/jquery.mobile-1.2.0.min.js"></script>  

        <script>

            function validateForm(form)
            {
                if ($('input[name=city]:checked').val() === "0")
                {
                    getCoordinates(form);
                    return false;
                }
                else
                {
                    return true;
                }
            }

            function getCoordinates(form) {
                if ($("#cityName").val() !== "" && $("#country").val() !== "") {
                    $.mobile.showPageLoadingMsg();
                    var cityName = "'" + $("#cityName").val() + "," + $("#country").val() + "'";
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'address': cityName}, function(results,
                            status) {
                        if (status === google.maps.GeocoderStatus.OK) {                            
                            $("#lat").val(results[0].geometry.location.lat());
                            $("#lon").val(results[0].geometry.location.lng());
                            form.submit();
                        } else {
                            alert("This city could not be found." + status);                            
                            $.mobile.hidePageLoadingMsg();
                            return false;
                        }
                    });


                }
                 else if ($("#country").val() === "") {
                    alert("Please give a country");
                }
                else if ($("#cityName").val() === "") {
                    alert("Please give a city");
                }
                return false;
            }

            $(document).ready(function() {
                $('input[type=radio][name=city]').change(function() {
                    if ($(this).attr('id') === "city0") {
                        $('#addNewCity').fadeIn();
                    }
                    else
                        $('#addNewCity').fadeOut();
                });
            });
        </script>

        <?php
        include_once 'Config.php';
        include_once CLASSES . 'Database.class.php';
        include_once CLASSES . 'App.class.php';
        include_once CLASSES . 'City.class.php';
        include_once CLASSES . 'Dataset.class.php';

        Database::connect();
        Database::begin();
        $userDatasetsCount = Dataset::getDatasetsOfUser($userId);
        ?>
        <?php
        if ($userDatasetsCount + 1 > MAX_DATASETS_PER_USER) {
            $userDatasetUploadLimitReached = true;
            ?>
            <script>
                $(document).ready(function() {
                    $('[type="submit"]').button('disable');
                    $("input[type='text']").textinput('disable');
                    $("input[type='url']").textinput('disable');
                    $("input[type='radio']").checkboxradio('disable');
                });
            </script>
            <?php
        }
        ?>
    </head>       
    <body>
        <div data-role="page">
            <div data-role="header">
                <h1>Import Dataset</h1>
            </div>
            <div data-role="content"> 
                <div id="importNewDatasetWrapper">
                    <?php
                    // define variables and set to empty values
                    $datasetTypeErr = $datasetUrlErr = $datasetNameErr = $datasetFileErr = $cityErr = "";
                    $latitude = $longitude = $cityName = $newUid = $datasetName = $city = $datasetUrl = $datasetType = $datasetFile = "";

                    $error = false;
                    $creatingNewCity = false;

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (empty($_POST["city"]) && empty($_POST["newCity"])) {
                            $cityErr = "City is required";
                            $error = true;
                        } else if (!empty($_POST["city"]) && !empty($_POST["newCity"])) {
                            $cityErr = "Only one city must be selected";
                            $error = true;
                        } else if (!empty($_POST["city"])) {
                            $city = $_POST["city"];
                        } else {
                            $creatingNewCity = true;
                            $cityName = clear_input($_POST["newCity"]);
                            $latitude = $_POST["latitude"];
                            $longitude = $_POST["longitude"];
                        }

                        if (empty($_POST["datasetName"])) {
                            $datasetNameErr = "Dataset Name is required";
                            $error = true;
                        } else {
                            $datasetName = clear_input($_POST["datasetName"]);
                        }

                        if (empty($_POST["datasetUrl"]) && empty($_FILES["userFile"]["name"])) {
                            $datasetUrlErr = "Dataset Url or Dataset file is required";
                            $error = true;
                        } else if (!empty($_POST["datasetUrl"]) && !empty($_FILES["userFile"]["name"])) {
                            $datasetUrlErr = "Only one of a Dataset Url OR a Dataset file must be given";
                            $error = true;
                        } else if (!empty($_POST["datasetUrl"])) {
                            $datasetUrl = clear_input($_POST["datasetUrl"]);
                        } else {

                            $datasetFile = $_FILES['userFile'];


                          /*if($datasetFile['type'] != "application/json")
                          {
                                $datasetFileErr = "Dataset file must be in json format (.json)";
                                $error = true;
                            }
                          else{*/
					
                            $target_Path =  HTDOCS_ROOT.BASE_DIR."data/";
                            $target_Path = $target_Path.basename( $datasetFile['name'] );
                            move_uploaded_file( $datasetFile['tmp_name'], $target_Path );
                            $datasetUrl = SERVERNAME . BASE_DIR."data/".$datasetFile['name'];
                         /* }*/
                        }

                        if (empty($_POST["datasetType"])) {
                            $datasetTypeErr = "Dataset Type is required";
                            $error = true;
                        } else {
                            $datasetType = clear_input($_POST["datasetType"]);
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
                        <?php
                        if ($userDatasetUploadLimitReached) {
                            echo '<p>Hi <b>' . $username . '</b>! You have uploaded <b>' . $userDatasetsCount . '</b> dataset(s) so far. You are not allowed to upload any more datasets.</p>';
                            echo '<br/><a data-ajax="false" class="ui-link" href="appForm.php" >Back to the app creation form</a>';
                        }
                        else {
                        ?>
                        <form id="importNewDataset" data-ajax="false" onsubmit="return validateForm(this);"                       
                              method="post" action="importDatasets.php" enctype='multipart/form-data'>                         
                          <p>
                            <?php if(!$general->logged_in()){?>
                            <div class="warning">
                              <a target="_blank" href="http://atc-dnn.atc.gr/citadel-eu/Login/tabid/91/language/en-US/Default.aspx" relation="external">You have to login before importing a dataset!</a>
                            </div>
                                <?php } else { 
                                  echo 'Hi <b>' . $username . '</b>! You have uploaded <b>' . 
                                       $userDatasetsCount . '</b> dataset(s) so far. You are allowed to upload a maximum of <b>' . 
                                      MAX_DATASETS_PER_USER . '</b> datasets.';   
                                  
                                  echo '<a style="float:right" href="logout.php" data-ajax="false">log out</a>';
                                } ?>
                          </p>  

                            <p><span class="error">* required field.</span></p>

                            <b>Dataset Name:</b> <span class="error">* <?php echo $datasetNameErr; ?></span><br/><br/>
                            <input type="text" id="dat" name="datasetName" required>

                            <br><br>

                            <div style="border:1px solid #CCC;padding:0 20px 20px;border-radius:10px;">
                                <h3>You must either enter a dataset url or upload a dataset file in 
                                    <a href="http://www.citadelonthemove.eu/en-us/citadelcommonschemaforpois.aspx" target="_blank"
                                       rel="external">Citadel Json format</a></h3>
                                <b>Dataset Url:</b> <span class="error">* <?php echo $datasetUrlErr; ?></span><br/>
                                <input type="url" name="datasetUrl" ><br />
                                <b>Dataset File:</b><span class="error">* <?php echo $datasetFileErr; ?></span><br/>
                                <input type='file' name='userFile'>
                                <a href="http://www.rbox.tv/citadel/converter/php/" target="_blank" rel="external">
                                    Click here to easily convert your dataset in the Citadel Json format!</a>
                            </div>                        

                            <br><br>

                            <b>Dataset Type:</b> (e.g. Pois, Parking, Events, etc) <span class="error">* <?php echo $datasetTypeErr; ?></span><br/><br/>
                            <input type="text" name="datasetType" required>


                            <br><br>

                            <!--div style="border:1px solid #CCC;padding:0 20px 20px;border-radius:10px;">
                                <h3>You must either select a city or add a new one </h3-->
                            <b>Select a city:</b> <span class="error">* <?php echo $cityErr; ?></span><br/><br/>
                            <div id="datasetsCheckboxes" data-role=controlgroup>                       
                                <?php
                                $sql = 'SELECT * FROM cities ORDER BY name';
                                foreach (Database::$dbh->query($sql) as $row) {
                                    echo '<input type="radio" name="city" id="city' . $row['id'] . '" value="' . $row['id'] . '">
                                <label for="city' . $row['id'] . '">' . $row['name'] . '</label>';
                                }
                                ?>
                                <input type="radio" name="city" id="city0" value="0">
                                <label for="city0">Add a new city</label>
                            </div>

                            <br><br>

                            <div id="addNewCity" style="display:none;">   
                                <b>Country:</b><span class="error">*</span>
                                <input type="text" id="country" name="country" value="">
                                <b>City:</b><span class="error">*</span>
                                <input type="text" id="cityName" name="newCity" value="">

                                <input type="text" style="display:none;" id="lat" name="latitude" value="">
                                <input type="text" style="display:none;" id="lon" name="longitude" value="">
                            </div> 

                            <!--/div--> 
                            <?php if(!$general->logged_in()){?>
                              <a target="_blank" href="http://atc-dnn.atc.gr/citadel-eu/Login/tabid/91/language/en-US/Default.aspx" relation="external">You have to login before importing a dataset!</a>
                            <?php } 
                             else { ?>
                              <input type="submit" name="import" value="Import">
                            <?php }?>  
                            
                            <br><br>
                            <?php echo '<a style="float:right" href="appForm.php" >Back to the app creation form</a>'; ?>        
                        </form>
                    <?php 
                        } // end if  $userDatasetUploadLimitReached
                      } // end if ($_SERVER["REQUEST_METHOD"] != "POST" || $error)  ?>

                    <?php
                    if (isset($_POST['import']) || $_SERVER["REQUEST_METHOD"] == "POST") {
                        if (!$error && ($userDatasetsCount < MAX_DATASETS_PER_USER)) {                        
                            if ($creatingNewCity) {
                                $city = City::saveNewCity($cityName, $latitude, $longitude);
                            }
                            Dataset::saveNewDataset($datasetName, $datasetUrl, $datasetType, $city, $userId, $latitude, $longitude);
                            Database::commit();
                            echo '<div class="success">Your dataset was imported successfully!';
                            echo '<br><br>';
                            echo ' <a href="appForm.php" rel="external" >Back to the app creation form</a></div>';
                        }
                    }
                    Database::disconnect();
                    ?>             
                </div>
            </div>
        </div>

    </body>
</html>




