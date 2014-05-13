<?php
include_once 'Config.php';
include_once CLASSES . 'App.class.php';
include_once CLASSES . 'Database.class.php';
include_once 'defineColor.php';
include_once CLASSES . 'cities.php';

$currentAppName = "Loading app...";
$appID = isset($_GET['uid']) ? $_GET['uid'] : '';
$colors = printColors($appID);
?>

<!DOCTYPE html>
<html>
    <head> 
        <title>App Generation Tool</title> 
        <!--------------- Metatags ------------------->   
        <meta charset="utf-8" />
        <!-- Not allowing the user to zoom -->    
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1.0,maximum-scale=1.0, minimum-scale=1.0"/>
        <!-- iphone-related meta tags to allow the page to be bookmarked -->
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <meta name="description" content="Mobile Application app generated by the Citadel AGT.">  
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <!--------------- Facebook metatags------------>
        <meta property="og:title" content="Citadel... on the move"/>
        <meta property="og:site_name" content="Citadel Application generation Tool"/>
        <meta property="og:image" content="<?php echo SERVERNAME . BASE_DIR ?>images/logoCitadel.png"/>

        <!--------------- CSS files ------------------->    
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />        
        <link rel="stylesheet" href="css/app-generator.min.css">
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile.structure-1.2.0.min.css" /> 
        <link rel="stylesheet" href="css/my.css" />
        <!--<link rel="stylesheet" href="css/validationForm.css" />-->

        <style>
            .ui-btn-up-a  {
                background: linear-gradient(<?php echo $colors['color'] ?>, <?php echo $colors['color'] ?>) repeat scroll 00 <?php echo $colors['color'] ?>;
            }
            .ui-btn-hover-a{
                background: <?php echo $colors['darkColor'] ?>;
            }
            .ui-btn-active{
                background: <?php echo $colors['darkColor'] ?>;
            }
            .list-scroll-container{
                background: none repeat scroll 0 0 <?php echo $colors['color'] ?>;
            }
            .ui-checkbox-on .ui-icon,
            .ui-radio-on .ui-icon {
                background-color: <?php echo $colors['color'] ?> 
            }
            #list ul li.ui-btn-up-b {
                background: <?php echo $colors['color'] ?> ;
            }
            .ui-btn-hover-a, .ui-btn-hover-b {
                border:1px solid <?php echo $colors['darkColor'] ?>  ;
                background:<?php echo $colors['darkColor'] ?> ;
            }
            .ui-focus, .ui-btn:focus {
                box-shadow: 0 0 3px <?php echo $colors['darkColor'] ?> inset,
                    0 0 9px <?php echo $colors['darkColor'] ?>;
            }

        </style>

        <!--------------- Javascript dependencies -------------------> 
        <!-- Google Maps JavaScript API v3 -->    
        <script type="text/javascript"
                src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry">
        </script>
        <!-- Google Maps Utility Library - Infobubble -->     
        <script type="text/javascript"
                src = "http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble.js">
        </script>


        <!-- Overlapping markers Library: Deals with overlapping markers in Google Maps -->
        <script src="js/oms.min.js"></script>  
        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>
        <!-- jQuery Mobile Library -->
        <script src="js/jquery.mobile-1.2.0.min.js"></script>  
        <!-- Page params Library: Used to pass query params to embedded/internal pages of jQuery Mobile -->    
        <script src="js/jqm.page.params.js"></script>
        <!-- Template specific functions and handlers -->    
        <script src="js/app-generator-lib.js"></script>  
        <script src="js/jquery.ajax-progress.js"></script>  

        <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
        <script type="text/javascript">stLight.options({publisher: "ur-cf1bb4ba-7f06-2a05-b666-52afb3356a9c", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>    


        <!-- /Progress Bar CSS file -->
        <link rel="stylesheet" type="text/css" href="css/jQMProgressBar.css" />
        <!-- /Progress Bar JS file -->
        <script type="text/javascript" src="js/jQMProgressBar.js"></script>

    </head> 

    <body>
        <!-- Home Page: Contains the Map -->
        <div data-role="page" id="page1" class="page">
           
            <header data-role="header" data-posistion="fixed" data-id="constantNav" data-fullscreen="true">
                <span class="ui-title"><?php echo $currentAppName ?></span>
                <a href="" id="filter" data-icon="search" data-iconpos="left" data-theme="a" title="Categories" class="ui-btn-left">Categories</a>
                <a href="" id="city" data-icon="bars" data-iconpos="left" data-theme="b" title="Select City" class="ui-btn-right">Cities</a>              

                <!--a href="#info" data-rel="dialog" data-icon="info" data-iconpos="notext" data-theme="b" title="Info" class="ui-btn-right">&nbsp;</a-->
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme ui-btn-active" data-theme="a">Map</a></li>
                        <!--li><a href="#" class="pois-showall" data-theme="a">Show all</a></li-->
                        <li><a href="#page2" class="pois-list" data-theme="a">List</a></li>
                    </ul>
                </div><!-- /navbar -->
            </header>


            <div data-role="content" id="map-filter">
                <div class="filters-list" id="mapFilterList">
                    <fieldset data-role="controlgroup" data-mini="true" data-theme="a">
                        <!-- dynamically filled with data -->
                    </fieldset>
                </div>
                <footer data-role="footer" data-posistion="fixed" data-fullscreen="true" class="filter-footer">
                    <a href="" id="apply" data-icon="gear" data-theme="a" title="Apply" class="ui-btn-right">Apply</a>
                </footer>
            </div><!--map-filter-->

            <div data-role="content" id="city-filter">
                <div class="filters-list" id="cityFilterList">
                    <fieldset data-role="controlgroup" data-mini="true" data-theme="a">

                    </fieldset>
                </div>
                <!--footer data-role="footer" data-posistion="fixed" data-fullscreen="true" class="filter-footer">
                    <a href="" id="cityApply" data-icon="gear" data-theme="a" title="Apply" class="ui-btn-right">Apply</a>
                </footer-->
            </div><!--city-filter-->

            <div data-role="content" id="map-container">

 		<!-- /Progress Bar for jQuery Mobile -->
                <div id="progressbar"></div>

                <div id="map_canvas" class="map_canvas"></div>
            </div>

            <footer data-role="footer" data-posistion="fixed" data-fullscreen="true">
                <span class='st_facebook_large' displayText='Facebook'></span>
                <span class='st_twitter_large' displayText='Tweet'></span>
            </footer>

        </div>

        <!-- List Page: Contains a list with the results -->
        <div data-role="page" id="page2" class="page">

            <header data-role="header" data-posistion="fixed" data-id="constantNav">
                <span class="ui-title"><?php echo $currentAppName ?></span>
                <fieldset data-role="controlgroup" class="favourites-button">
                    <input type="checkbox" name="favourites" id="favourites" class="custom" />
                    <label for="favourites">Favourites</label>
                </fieldset>
                <a href="" data-icon="back" data-iconpos="notext" data-theme="a" title="Back" data-rel="back" class="ui-btn-right">&nbsp;</a>
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="a">Map</a></li>
                        <!--li><a href="#" class="pois-showall" data-theme="a">Show all</a></li-->
                        <li><a href="#page2" class="pois-list ui-btn-active" data-theme="a">List</a></li>
                    </ul>
                </div><!-- /navbar -->
            </header>

            <div class="list-container">
                <div class="list-scroll-container">
                    <div data-role="content" id="list" class="poi">
                        <ul data-role='listview' data-filter='true' data-theme='b'>
                            <!-- dynamically filled with data -->
                        </ul>
                    </div><!--list-->
                </div><!--list-scroll-container-->
            </div><!--list-container-->
        </div><!-- /page -->

        <!-- Details Page: Contains the details of a selected element -->
        <div data-role="page" id="page3" data-title="Event fullstory page title" class="page">
            <header data-role="header" data-posistion="fixed" data-fullscreen="true">
                <span class="ui-title"><?php echo $currentAppName ?></span>
                <a href="" data-icon="back" data-iconpos="notext" data-theme="a" title="Back" data-rel="back" class="ui-btn-right">&nbsp;</a>
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="a">Map</a></li>
                        <!--li><a href="#" class="pois-showall" data-theme="a">Show all</a></li-->
                        <li><a href="#page2" class="pois-list" data-theme="a">List</a></li>
                    </ul>
                </div><!-- /navbar --> 
            </header>

            <div class="list-container">
                <div class="list-scroll-container">
                    <div data-role="content" id="item">
                        <!-- dynamically filled with data -->
                    </div><!--item-->
                    <ul><li><div class='votePanel'>
                                <p>Rate this POI</p>
                                <span class="voteScoreWrapper">
                                    <img id='voteUpButton'  class='voting-icon'  src='images/like-32.png'  alt='Vote up' />
                                    <span id='upVoteScore'><img  src='images/loader.png'  /></span>
                                </span><span  class="voteScoreWrapper" id="voteDownScoreWrapper">
                                    <img  class='voting-icon'  id='voteDownButton'  src='images/dislike-32.png' alt='Vote down' />
                                    <span id='downVoteScore'><img  src='images/loader.png'  /></span>
                                </span>
                            </div>
                        </li>
                    </ul>
                    <form id="insertVote">

                        <div data-role="content" style="display:none;">
                            <div data-role="fieldcontain">

                                <input type="text" name="poiId" id="poiIdForVote" required />
                            </div>
                            <div data-role="fieldcontain">

                                <input type="text" name="poiVote" id="poiVote" required/>
                            </div>
                        </div><!--list-scroll-container-->
                    </form>

                </div><!--list-container-->

                <footer data-role="footer" data-posistion="fixed" data-fullscreen="true">
                    <a href="" id="addFav" data-icon="star" data-theme="a" title="Add to favourites" data-rel="star" class="ui-btn-center">Add to favourites</a>
                    <a href="" id="removeFav" data-icon="star" data-theme="a" title="Remove from favourites" data-rel="star" class="ui-btn-center">Remove from favourites</a>
                </footer>

            </div>
        </div><!-- /page -->

        <script type="text/javascript">

            /****************** Global js vars ************************/
            /* GLobal map object */
            var map;
            /* List of pois read from json object */
            var pois = [];
            /* List of dataset metadata read from json object */
            var meta = {};
            /* Holds all markers */
            var markersArray = [];
            /* Holds filters */
            var filters = [];

            /*The id (unique identifier) of the application*/
            var appId = "<?php echo $appID; ?>";

            /* Define cities - get them from db */
            var cities = <?php printSelectedCities($appID); ?>;

            /* Keeps page id to emulate full url using querystring */
            var pageId = 0;

            /* Set infoBubble/infoWindow global variable */
            var infoBubble;

            /* Defines the color of the infoBubble/infoWindow*/
            var bubbleColor = <?php echo "'" . $colors['darkColor'] . "'" ?>;

            /* The coordinates of the center of the map */
            var mapLat = <?php echo MAP_CENTER_LATITUDE; ?>;
            var mapLon = <?php echo MAP_CENTER_LONGITUDE; ?>;
            var mapZoom = <?php echo MAP_ZOOM; ?>;
            var maxCityDistance = <?php echo MAX_CITY_DISTANCE_KM; ?>;

            var insertNewPoiScript = "<?php echo SERVERNAME . BASE_DIR . CLASSES_DIR . "insert.php"; ?>";
            var insertNewVoteScript = "<?php echo SERVERNAME . BASE_DIR . CLASSES_DIR . "voteManager.php"; ?>";
            var getPoiVotesScript = "<?php echo SERVERNAME . BASE_DIR . CLASSES_DIR . "voteManager.php"; ?>";


            /* Just call the initialization function when the page loads */
            $(window).load(function() {
                globalInit();
            });

        </script>
    </body>
</html>