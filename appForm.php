<?php
include_once 'Config.php';
include_once CLASSES . 'resizeImage.php';
require CLASSES . 'init.php';
//$general->logged_out_protect();

/* Read user info from session in case login comes from
 * citadel website
 */
if (isset($_SESSION['username'])) {
    $userId = $_SESSION['id'];
    $username = $_SESSION['username'];
} else { // use built-in login functionality
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
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.css" />
        <link rel="stylesheet" href="css/my.css" />  
        <link rel="stylesheet" href="css/spectrum.css" />

        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>

        <!-- jQuery Mobile Library -->
        <script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.js"></script>

        <script src="js/spectrum.js"></script> 

        <script lang="text/javascript">


            $(document).ready(function() {

                $.ajax({
                    type: "GET",
                    url: "http://www.citadelonthemove.eu/DesktopModules/DatasetLibrary/API/Service/GetCities",
                    cache: false,
                    error: getCitiesFailure,
                    dataType: "jsonp"
                });


                $('.ui-checkbox a').bind("click", function(event, data) {
                    event.stopPropagation();
                    window.open($this.attr('href'), $this.attr('target'));
                });


                $("#createNewAppForm").submit(function(event) {

                    getSelectedDatasetIds();

                });


                $("#citySelection").bind("change", function() {
                    var selectedCityName = $(this).find(":selected").text();
                    if ($(this).find(":selected").val() !== "-1")
                    {
                        $.ajax({
                            type: "GET",
                            url: "http://www.citadelonthemove.eu/DesktopModules/DatasetLibrary/API/Service/GetCityCategoriesAndDatasets?city=" + encodeURIComponent(selectedCityName),
                            cache: false,
                            error: getCitiesFailure,
                            dataType: "jsonp"
                        });
                    }
                });


                // Swipe to remove list item
                $(document).on("swipeleft swiperight", "#datasetsListView li", function(event) {
                    var listitem = $(this),
                            // These are the classnames used for the CSS transition
                            dir = event.type === "swipeleft" ? "left" : "right",
                            // Check if the browser supports the transform (3D) CSS transition
                            transition = $.support.cssTransform3d ? dir : false;
                    confirmAndDelete(listitem, transition);
                });
                // If it's not a touch device...
                if (!$.mobile.support.touch) {
                    // Remove the class that is used to hide the delete button on touch devices
                    $("#datasetsListView").removeClass("touch");
                    // Click delete split-button to remove list item
                    $(".delete").on("click", function() {
                        var listitem = $(this).parent("li");
                        confirmAndDelete(listitem);
                    });
                }



            }); // document.ready

            function confirmAndDelete(listitem, transition) {
                // Highlight the list item that will be removed
                listitem.children(".ui-btn").addClass("ui-btn-active");
                // Inject topic in confirmation popup after removing any previous injected topics
                $("#confirm .topic").remove();
                listitem.find(".topic").clone().insertAfter("#question");
                // Show the confirmation popup
                $("#confirm").popup("open");
                // Proceed when the user confirms
                $("#confirm #yes").unbind("click");
                $("#confirm #yes").on("click", function() {
                    // Remove with a transition
                    datasetsCounter--;
                    $("#datasetsCounter").html("(" + datasetsCounter + ")");

                    if (transition) {
                        listitem
                                // Add the class for the transition direction
                                .addClass(transition)
                                // When the transition is done...
                                .on("webkitTransitionEnd transitionend otransitionend", function() {
                            // ...the list item will be removed
                            listitem.remove();
                            // ...the list will be refreshed and the temporary class for border styling removed
                            $("#datasetsListView").listview("refresh").find(".border-bottom").removeClass("border-bottom");
                        })
                                // During the transition the previous button gets bottom border
                                .prev("li").children("a").addClass("border-bottom")
                                // Remove the highlight
                                .end().end().children(".ui-btn").removeClass("ui-btn-active");
                    }
                    // If it's not a touch device or the CSS transition isn't supported just remove the list item and refresh the list
                    else {
                        listitem.remove();
                        $("#datasetsListView").listview("refresh");
                    }

                });
                // Remove active state and unbind when the cancel button is clicked
                $("#confirm #cancel").on("click", function() {
                    listitem.removeClass("ui-btn-active");
                    $("#confirm #yes").off();
                });
            }

            var cities_html = "";
            var categories_html = "";
            var datasets_html = "";
            var datasetsCounter = 0;
            var categoryName = "";

            function getCitiesJsonPSuccess(data)
            {
                cities_html += "<option value='-1'>- Select City/Region -</option>";
                $.each(data.cities, function(i, city) {
                    cities_html += "<option lat='" + city.lat + "'  lon='" + city.lon + "' value='" + i + "'>" + city.name + "</option>";
                });

                $('#citySelection').html(cities_html);

                var myselect = $("select#citySelection");

                if (myselect[0]) {
                    myselect[0].selectedIndex = 0;
                }
                myselect.selectmenu("refresh");
            }

            function getCitiesFailure(data, status)
            {
                
            }

            function getCategoriesAndDatasetsJsonPSuccess(data)
            {
                categories_html = "";

                $.each(data.categories, function(i, category) {

                    categoryName = i;
                    categories_html += "<div id='" + i + "' data-role='collapsible' data-inset='false'><h3>" + i + "</h3><ul data-role='listview'>";
                    datasets_html = "";
                    $.each(category, function(j, dataset) {
                        datasets_html += "<li class='selectedDataset'><a class='ui-btn ui-btn-icon-right ui-icon-plus' href='#'>" + dataset.title + "</a><span class='datasetId' style='display:none;'>" + dataset.id + "</span><span class='datasetUrl' style='display:none;'>" + dataset.url + "</span><span style='display:none;' class='categoryName'>" + categoryName + "</span></li>";
                        //datasets_html += "<li class='selectedDataset'><a class='ui-btn ui-btn-icon-right ui-icon-plus' href='#'>" + dataset.title + "</a><span class='datasetId' style='display:none;'>" + dataset.id + "</span><span class='datasetUrl' style='display:none;'>" + dataset.url + "</span></li>";
                    });

                    datasets_html = datasets_html + "</ul></div>";
                    categories_html += datasets_html;
                });

                $('#datasetsByCategory').html(categories_html);
                $("ul").listview();
                $('#datasetsByCategory').collapsibleset('refresh');
                $(".selectedDataset").on("click", function() {

                    addDatasetToCart($("a", this).html(), $("span.datasetId", this).html(), $("span.datasetUrl", this).html(), $("span.categoryName", this).html(), $("#citySelection").find(":selected").text(), $("#citySelection").find(":selected").attr('lat'), $("#citySelection").find(":selected").attr('lon'));
                    // addDatasetToCart($("a", this).html(), $("span.datasetId", this).html(), $("span.datasetUrl", this).html(), $("#citySelection").find(":selected").text(), $("#citySelection").find(":selected").attr('lat'), $("#citySelection").find(":selected").attr('lon'));
                });

            }


            function addDatasetToCart(datasetName, datasetId, datasetUrl, categoryName, cityName, cityLat, cityLon)
            {
                // see if element(s) exists that matches by checking length           
                var exists = $('#datasetsListView li:contains(' + datasetName + ')').length;

                if (!exists) {
                    datasetsCounter++;
                    var datasetTitle = '<li><span style="display:none;" class="datasetId">' + datasetId + '</span><span style="display:none;" class="cityName">' + cityName + '</span><span style="display:none;" class="cityLat">' + cityLat + '</span><span style="display:none;" class="cityLon">' + cityLat + '</span><span style="display:none;" class="categoryName">' + categoryName + '</span><a><div class="topic">' +
                            '<h2>' + datasetName + ' , <i><span style="font-size:12px">(' + datasetUrl + ')</span></i></h2></div></a><a href="#" class="delete">Delete</a>' +
                            '</a></li>';

                    $('#datasetsListView').append(datasetTitle).listview("refresh");

                    $("#datasetsCounter").html("(" + datasetsCounter + ")");
                    $(".delete").unbind("click");
                    $(".delete").on("click", function() {
                        var listitem = $(this).parent("li");
                        confirmAndDelete(listitem);
                    });
                }
            }


            function getSelectedDatasetIds()
            {
                var datasetIds = [];
                var cityNames = [];
                var cityLats = [];
                var cityLons = [];
                var cities = [];
                var categoryNames = [];

                $('#datasetsListView li').each(function() {
                    datasetIds.push($("span.datasetId", this).html());
                    if ($.inArray($("span.categoryName", this).html(), categoryNames) === -1)
                    {
                        categoryNames.push($("span.categoryName", this).html());
                    }
                    if ($.inArray($("span.cityName", this).html(), cityNames) === -1)
                    {
                        cityNames.push($("span.cityName", this).html());
                        cityLats.push($("span.cityLat", this).html());
                        cityLons.push($("span.cityLon", this).html());
                    }
                });
                var i;

                for (i = 0; i < cityNames.length; ++i) {
                    var city = new City();
                    city.name = cityNames[i];
                    city.lat = cityLats[i];
                    city.lon = cityLons[i];
                    cities.push(city);
                }

                $('#datasetIds')
                        .attr('name', "datasetIds")
                        .attr('value', datasetIds);

                $('#categoryNames')
                        .attr('name', "categoryNames")
                        .attr('value', categoryNames);

                $('#cities')
                        .attr('name', "cities")
                        .attr('value', JSON.stringify(cities));
            }
            function City() {
                this.name;
                this.lat;
                this.lon;
            }
        </script>

    </head>


    <body>
        <div data-role="page">
            <div data-role="header">
                <h1>Create your app</h1>
                <div class="beta"></div> 
            </div>

            <div data-role="content"> 

                <div id="createNewAppWrapper">
                    <?php
                    include_once 'Config.php';
                    include_once CLASSES . 'Database.class.php';
                    include_once CLASSES . 'App.class.php';

                    Database::connect();

                    // define variables and set to empty values
                    $darkColorErr = $colorErr = $nameErr = $descriptionErr = $cityIdsErr = $datasetIdsErr = "";

                    $error = false;
                    // Check all required fields
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                        if (empty($_POST["datasetIds"])) {
                            $datasetIdsErr = "Dataset is required";
                            $error = true;
                        }
                        if (empty($_POST["name"])) {
                            $nameErr = "Application Name is required";
                            $error = true;
                        }
                        if (empty($_POST["description"])) {
                            $descriptionErr = "Application Description is required";
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
                            $image = null;
                            if ((isset($_POST["name"])) && (isset($_POST["description"])) &&
                                    (isset($_POST["datasetIds"])) && (isset($_POST["color"])) &&
                                    (isset($_POST["darkColor"]))) {

                                if (isset($_FILES["image"])) {
                                    $tmpFile = $_FILES['image']['tmp_name'];
                                    $storeImage = false;
                                    if (!isset($tmpFile)) {
                                        
                                    } else {
                                        $fileName = $_FILES['image']['name'];
                                        $fileSize = $_FILES['image']['size'];
                                        $filePath = MY_PATH . $fileName;

                                        //Check filesize
                                        if ($fileSize < 100000) {

                                            move_uploaded_file($tmpFile, $filePath);

                                            /* Determine filetype (jpeg, png, gif) */
                                            switch ($_FILES['image']['type']) {
                                                case 'image/jpeg': $ext = "jpg";
                                                    $gd = imagecreatefromjpeg($filePath);
                                                    $filePath = str_replace(".jpg", ".png", $filePath);
                                                    $storeImage = true;
                                                    break;
                                                case 'image/png': $ext = "png";
                                                    $gd = imagecreatefrompng($filePath);
                                                    $storeImage = true;
                                                    break;
                                                case 'image/gif': $ext = "gif";
                                                    $gd = imagecreatefromgif($filePath);
                                                    $filePath = str_replace(".gif", ".png", $filePath);
                                                    $storeImage = true;
                                                    break;
                                            }

                                            /* Resize image and store it like png at the server */
                                            if ($storeImage) {
                                                $resized = resizePreservingAspectRatio($gd, 60, 60);
                                                imagepng($resized, $filePath);
                                                $image = base64_encode(file_get_contents($filePath));
                                            }
                                        }
                                    }
                                }

                                $newApp = App::createFromArray($_POST, $userId, $image);
                                if ($newApp->save()) {
                                    Database::commit();
                                    echo '<div class="success">Your application was created successfully!';
                                    echo '<br><br>';
                                    echo ' <a href="index.php?uid=' . $newApp->uid . '" target="_blank" rel="external">See my app!</a>';
                                    echo ' <a style="float:right" href="appForm.php" rel="external">Create a new app</a></div>';
                                } else {
                                    Database::rollback();
                                    echo '<div class="failure">Your application was not created.';
                                    echo '<br><br>';
                                    echo ' <a style="float:right" href="appForm.php" >Create a new app</a></div>';
                                }
                                Database::disconnect();
                            }
                        }
                    }
                    ?>

                    <?php if ($_SERVER["REQUEST_METHOD"] != "POST" || $error) { ?>
                        <form id="createNewAppForm" data-ajax="false" enctype="multipart/form-data" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                            <p>
                                <?php if (!$general->logged_in()) { ?>
                                <div class="warning">
                                    <a target="_blank" href="<?php echo CITADELLOGINLINK; ?>" relation="external">You have to login before creating an app!</a>
                                </div>
                                <?php
                            } else {
                                echo 'Hi <b>' . $username . '</b>! Use this form to create your own app.';                                                            }
                            ?>
                            </p>                            

                            <p><span class="error">* required field.</span></p>                        

                            <legend><b>Select City/Region:</b> <span class="error">* <?php echo $cityIdsErr; ?></span></legend>
                            <div class="ui-field-contain" id="citiesSelectMenu" > 
                                <select name="selectCity" id="citySelection"> 
                                </select>
                            </div>
                            <br>

                            <div data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-inset="false">
                                <legend><b>Selected datasets </b><span id="datasetsCounter">(0)</span></legend>
                                <ul id="datasetsListView" class="touch" data-role="listview" data-split-icon="delete">

                                </ul>
                            </div>

                            <br>

                            <p><span class="error">* required field.</span></p>                        

                            <legend><b>Select datasets grouped by category:</b> <span class="error">* <?php echo $datasetIdsErr; ?></span></legend><br/>
                            <?php
                            echo '<i>Select a Category to see the available datasets.</i>'
                            ?>
                            <br><br>

                            <div id="datasetsByCategory" data-role="collapsibleset" data-collapsed-icon="carat-d" data-expanded-icon="carat-u" data-inset="false">
                            </div>
                            <br><br>                       


                            <div id="confirm" class="ui-content" data-role="popup" data-theme="a">
                                <p id="question">Are you sure you want to remove this dataset?</p>
                                <div class="ui-grid-a">
                                    <div class="ui-block-a">
                                        <a id="yes" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">Yes</a>
                                    </div>
                                    <div class="ui-block-b">
                                        <a id="cancel" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">Cancel</a>
                                    </div>
                                </div>
                            </div><!-- /popup -->


                            <legend><b>Select the basic color of your app:</b><span class="error">* <?php echo $colorErr; ?></span></legend><br/>
                            <input type="color" class="full" name="color">

                            <br><br>

                            <legend><b>Select the secondary color (active/hovered buttons):</b><span class="error">* <?php echo $darkColorErr; ?></span></legend><br/>

                            <input type="color" class="full" name="darkColor">
                            <br><br>

                            <legend><b>Application Name:</b> <span class="error">* <?php echo $nameErr; ?></span></legend><br/>
                            <input type="text" name="name" required>                        
                            <br/><br/>

                            <legend><b>Application Description:(max 90 chars)</b> <span class="error">* <?php echo $descriptionErr; ?></span></legend><br/>
                            <textarea rows="4" cols="50" maxlength="90" name="description"   required ></textarea>                        
                            <br/><br/>

                            <legend><b>Application Image:</b></legend><br/>
                            <legend>(Supported image formats: gif, jpeg, png. Maximum size: 1MB.)</legend><br/>
                            <input type="file" name="image"/>
                            <br/><br/>

                            <input id="datasetIds" type="hidden"  />
                            <input id="categoryNames" type="hidden"  />
                            <input id="cities" type="hidden"  />

                            <?php if (!$general->logged_in()) { ?>
                                <a target="_blank" href="<?php echo CITADELLOGINLINK; ?>" relation="external">You have to login before creating an app!</a>
                            <?php } else {
                                ?>
                                <input type="submit" name="submit" value="Create the app" onclick="window.scrollTo(0, 0);">
                            <?php } ?>  

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




