<?php
include_once 'Config.php';
require CLASSES . 'init.php';
$general->logged_out_protect();
$user = $users->userdata($_SESSION['id']);
$username = $user['username'];
$userId = $user['id'];
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
        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>
        <!-- jQuery Mobile Library -->
        <script src="js/jquery.mobile-1.2.0.min.js"></script>  
        <?php
        include_once 'Config.php';
        include_once CLASSES . 'Database.class.php';
        include_once CLASSES . 'App.class.php';
        include_once CLASSES . 'Dataset.class.php';

        Database::connect();
        Database::begin();
        $userDatasetsCount = Dataset::getDatasetsOfUser($userId);
        ?>
        <?php
        if ($userDatasetsCount + 1 > MAX_DATASETS_PER_USER) {
            ?>
            <script>
                $(document).ready(function() {
                    $('[type="submit"]').button('disable');
                    $("input[type='text']").textinput('disable');
                    $("input[type='url']").textinput('disable');
                    $("input[type='radio']").checkboxradio('disable');
                });</script>
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
                    $newUid = $datasetName = $city = $datasetUrl = $datasetType = $datasetFile = "";

                    $error = false;
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (empty($_POST["city"])) {
                            $cityErr = "City is required";
                            $error = true;
                        } else {
                            $city = $_POST["city"];
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
                        } 
                        else if(!empty($_POST["datasetUrl"]) && !empty($_FILES["userFile"]["name"])){
                            $datasetUrlErr = "Only one of a Dataset Url OR a Dataset file must be given";
                            $error = true;
                        }                        
                        else if(!empty($_POST["datasetUrl"])){
                            $datasetUrl = clear_input($_POST["datasetUrl"]);
                        }
                        else
                        { 

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
                        <form id="importNewDataset" data-ajax="false"  method="post" action="importDatasets.php" enctype='multipart/form-data'>
                            <p>
                                <?php
                                echo 'Hi <b>' . $username . '</b>! You have uploaded <b>' . $userDatasetsCount . '</b> dataset(s) so far. You are allowed to upload a maximum of <b>' . MAX_DATASETS_PER_USER . '</b> datasets.';
                                ?>
                            </p>

                            <p><span class="error">* required field.</span></p>

                            <b>Dataset Name:</b> <span class="error">* <?php echo $datasetNameErr; ?></span><br/>
                            <input type="text" name="datasetName" required>

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
                                </a>
                            </div>                        

                            <br><br>

                            <b>Dataset Type:</b> (e.g. Pois, Parking, Events, etc) <span class="error">* <?php echo $datasetTypeErr; ?></span><br/>
                            <input type="text" name="datasetType" required

                                   <br><br>

                            <legend><b>Select a city:</b> <span class="error">* <?php echo $cityErr; ?></span></legend><br/>
                            <div id="datasetsCheckboxes" data-role=controlgroup>                       
                                <?php
                                $sql = 'SELECT * FROM cities ORDER BY name';
                                foreach (Database::$dbh->query($sql) as $row) {
                                    echo '<input type="radio" name="city" id="city' . $row['id'] . '" value="' . $row['id'] . '">
                                <label for="city' . $row['id'] . '">' . $row['name'] . '</label>';
                                }
                                ?>
                            </div>

                            <br><br>
                            <input type="submit" name="import" value="Import">
                            <br><br>
                            <?php echo '<a style="float:right" href="appForm.php" >Back to the app creation form</a></div>'; ?>        
                        </form>
                    <?php } // end if ?>

                    <?php
                    if (isset($_POST['import'])) {
                        if (!$error && ($userDatasetsCount < MAX_DATASETS_PER_USER)) {

                            Dataset::saveNewDataset($datasetName, $datasetUrl, $datasetType, $city, $userId);
                            Database::commit();
                            echo '<div class="success">Your dataset was imported successfully!';
                            echo '<br><br>'.$datasetName.'<br><br>'. $datasetUrl. '<br><br>'.
                                $datasetType.'<br><br>'. $city. '<br><br>'.$userId.'<br><br>';
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




