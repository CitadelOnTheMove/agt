<!DOCTYPE HTML>
<html>
    <head>
        <!--------------- Metatags ------------------->   
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--------------- CSS files ------------------->    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile.structure-1.2.0.min.css" /> 

        <style>
            .error {color: #FF0000;}
        </style>

        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>
        <!-- jQuery Mobile Library -->
        <script src="js/jquery.mobile-1.2.0.min.js"></script>  

    </head>       

    <body>

        <div data-role="page">
            <div data-role="header">
                <h1>Import Dataset</h1>
            </div>

            <div data-role="content"> 

                <div id="importNewDatasetWrapper">
                    <?php
                    include_once 'Config.php';
                    include_once CLASSES . 'Database.class.php';
                    include_once CLASSES . 'App.class.php';
                    include_once CLASSES . 'Dataset.class.php';

                    Database::connect();
                    Database::begin();


                    // define variables and set to empty values
                    $datasetTypeErr = $datasetUrlErr = $datasetNameErr = $datasetFileErr= $cityErr = "";
                    $newUid = $datasetName = $city = $datasetUrl = $datasetType = $datasetFile ="";

                     
                    $error = false;
                    // $formState = 'pending';
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

                        if (empty($_POST["datasetUrl"]) && empty($_FILES["userFile"])) {
                            $datasetUrlErr = "Dataset Url or Dataset file is required";
                            $error = true;
                        } 
                        else if(!empty($_POST["datasetUrl"]) && !empty($_FILES["userFile"])){
                            $datasetUrlErr = "Only one of a Dataset Url OR a Dataset file must be given";
                            $error = true;
                        }                        
                        else if(!empty($_POST["datasetUrl"])){
                              $datasetUrl = clear_input($_POST["datasetUrl"]);
                        }
                        else
                        { 
                          $datasetFile = $_FILES['userFile'];
                          
                          if($datasetFile['type'] != "application/json")
                          {
                            $datasetFileErr = "Dataset file must be in json format (.json)";
                            $error = true;
                          }
                          else{
                            $target_Path =  HTDOCS_ROOT.BASE_DIR."data/";
                            $target_Path = $target_Path.basename( $datasetFile['name'] );
                            move_uploaded_file( $datasetFile['tmp_name'], $target_Path );
                            $datasetUrl = SERVERNAME . BASE_DIR."data/".$datasetFile['name'];
                          }
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
                    <div id="testResponse"></div>
                    <form id="importNewDataset" data-ajax="false"  method="post" action="importDatasets.php" enctype='multipart/form-data'>
 
                        <p><span class="error">* required field.</span></p>

                        <b>Dataset Name:</b> <span class="error">* <?php echo $datasetNameErr; ?></span><br/>
                        <input type="text" name="datasetName" required>

                        <br><br>

                        <div style="border:1px solid #CCCCCC;padding:0 20px 20px;border-radius:10px;">
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
                        <br><br>
                        <input type="submit" name="import" value="Import">
                        <br><br>
                        <?php
                        if (isset($_POST['import'])) {
                            if (!$error) {

                                Dataset::saveNewDataset($datasetName, $datasetUrl, $datasetType, $city);
                                Database::commit();
                                echo '<span class="error">Your data were submitted successfully!</span>';
                            } 
                        }

                        Database::disconnect();
                        ?>
                        <br><br>
                        <a href="appForm.php<?php echo $newUid; ?>">Go back</a>
                    </form>

                </div>
            </div>
        </div>

    </body>
</html>




