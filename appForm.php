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
        <script lang="text/javascript">
        
        var counter=0;
        var counterMax = 0;
                    
            $(document).ready(function() {
              
              $('.ui-checkbox a').bind("click", function( event, data ){
                event.stopPropagation();             
                window.open($this.attr('href'), $this.attr('target'));     
              });
                
            
  
             $("input[type=checkbox][id^=city]").click(function() {
               
               /* The id attribute is of the form "cityXX"
                * where XX is the id of the city
                */ 
               var cityId = $(this).attr('id').substring(4);  
               
               if($(this).attr('checked')){
                
                /* Check if we allready have the datasets from previous 
                 * calls and if yes show them.
                 */
                if($('input[type=checkbox][name="dataset[]"].city'+cityId).length)
                {
                       $('input[type=checkbox][name="dataset[]"].city'+cityId).parent().show();
                }
                else{                                
                  $.ajax({
                         type: "GET",
                         url: "ajaxHelper.php?action=getCities&cityId=" + cityId,
                         cache: false,
                         success: onSuccess,
                         error: onFailure
                    }); 
                }
               } // if 
               else{ 
                 /*city checkbox unchecked, hide and uncheck the checkboxes */
                 $('input[type=checkbox][name="dataset[]"].city'+cityId).parent().hide();
                 $('input[type=checkbox][name="dataset[]"].city'+cityId).attr("checked", false).checkboxradio("refresh");
               }
             }); // click
                
            }); // document.ready

            function onSuccess(data, status)
            { 
               /* Add the new datasets to the existing ones */
                //var currentContent = $('#datasetsCheckboxes').html();
                $('#datasetsCheckboxes').append(data);

                /* Apply the checkbox styling to the new ones */
                $('input[type=checkbox][name="dataset[]"]').each(function() {
                  
                     if($(this).parent().not('.ui-checkbox').length)
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
            </div>
         
            <div data-role="content"> 

                <div id="createNewAppWrapper">
                    <?php
                    include_once 'Config.php';
                    include_once CLASSES . 'Database.class.php';
                    include_once CLASSES . 'App.class.php';

                    Database::connect();
                    Database::begin();

                    // define variables and set to empty values
                    $darkColorErr = $colorErr = $appNameErr = $cityErr = $datasetErr = "";
                    $newUid = $darkColor = $color = $appName = $city = $dataset = "";

                    // Check all required fields
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                        if (empty($_POST["city"])) {
                            $cityErr = "City is required";
                        } else {
                            $city = $_POST["city"];
                        }

                        if (empty($_POST["dataset"])) {
                            $datasetErr = "Dataset is required";
                        } else {
                            $dataset = ($_POST["dataset"]);
                        }

                        if (empty($_POST["appName"])) {
                            $appNameErr = "Application Name is required";
                        } else {
                            $appName = clear_input($_POST["appName"]);
                        }

                        if (empty($_POST["color"])) {
                            $colorErr = "Color is required";
                        } else {
                            $color = $_POST["color"];
                        }

                        if (empty($_POST["darkColor"])) {
                            $darkColorErr = "Dark Color is required";
                        } else {
                            $darkColor = $_POST["darkColor"];
                        }
                    }                     
           
                      if (isset($_POST['submit'])) {
                            if ((isset($_POST["city"])) && (isset($_POST["appName"])) && (isset($_POST["dataset"])) && (isset($_POST["color"])) && (isset($_POST["darkColor"]))) {
                             
                                $newUid = App::createNewApp($city, $appName, $dataset, $color, $darkColor);
                                Database::commit();                            
                                echo '<div class="success">Your application was created successfully!';
                                echo '<br><br>';
                                echo ' <a href="index.php?uid=' . $newUid . '" target="_blank">Click here to see it!</a>';
                                echo ' <a style="float:right" href="appForm.php" target="_blank">Create a new app</a></div>';                               
                            }
                        }           

                    function clear_input($data) {
                        $data = trim($data);
                        $data = stripslashes($data);
                        $data = htmlspecialchars($data);
                        return $data;
                    }
                    ?>
                   
                   <?php if ($_SERVER["REQUEST_METHOD"] != "POST") { ?>
                    <form id="createNewAppForm"  method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                        <p><span class="error">* required field.</span></p>                        

                        <legend><b>Select cities:</b> <span class="error">* <?php echo $cityErr; ?></span></legend><br/>
                        <div id="citiesCheckboxes"  data-role="controlgroup">

                            <?php
                            $sql1 = 'SELECT * FROM cities ORDER BY name';
                            foreach (Database::$dbh->query($sql1) as $row) {
                                echo '<input type="checkbox" autocomplete="off" name="city" id="city' . $row['id'] . '"  value="' . $row['id'] . '">
                            <label for="city' . $row['id'] . '">' . $row['name'] . '</label>';
                            }

                            ?>
                        </div>

                        <br><br>

                        <legend><b>Select datasets:</b> <span class="error">* <?php echo $datasetErr; ?></span> </legend><br/>
                   
                        <div id="datasetsCheckboxes" >
                            <?php
                            echo '<i>Select a City to see the available datasets.</i>'
                            ?>
                        </div>

                        <br>
                        <a href="#">Click here if you want to import a new dataset</a>
                        <br><br>


                        <legend><b>Select the basic color of your app:</b><span class="error">* <?php echo $colorErr; ?></span></legend><br/>
                        <div id="colorCheckboxes" data-role="controlgroup">
                            <?php
                            $sql2 = 'SELECT * FROM apps_available_colors order by name';
                            foreach (Database::$dbh->query($sql2) as $row) {
                                echo '<input type="radio" name="color" id="color' . $row['id'] . '" value="' . $row['id'] . '">
                                                         <label for="color' . $row['id'] . '">' . $row['name'] . '</label>';
                            }
                            ?>
                        </div>
                        <br><br>

                        <legend><b>Select the secondary color (active/hovered buttons):</b><span class="error">* <?php echo $darkColorErr; ?></span></legend><br/>
                        <div id="darkColorCheckboxes" data-role="controlgroup">
                            <?php
                            $sql3 = 'SELECT * FROM apps_available_darker_colors order by name';
                            foreach (Database::$dbh->query($sql3) as $row) {
                                echo '<input type="radio" name="darkColor" id="darkColor' . $row['id'] . '" value="' . $row['id'] . '">
                                    <label for="darkColor' . $row['id'] . '">' . $row['name'] . '</label>';
                            }
                            ?>
                        </div>
                        <br><br>

                        <legend><b>Application Name:</b> <span class="error">* <?php echo $appNameErr; ?></span></legend><br/>
                        <input type="text" name="appName">
                        <br><br>
                        <br><br>
                        <input type="submit" name="submit" value="Create the app">
                    
                        <?php
                        if (isset($_POST['submit'])) {
                            if ((isset($_POST["city"])) && (isset($_POST["appName"])) && (isset($_POST["dataset"])) && (isset($_POST["color"])) && (isset($_POST["darkColor"]))) {
                                Database::disconnect();                              
                            }
                        } ?>
                    </form>
                  
                   <?php } // end if?>
                </div>
            </div>
        </div>
    </body>
</html>




