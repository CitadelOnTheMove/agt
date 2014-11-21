<?php include_once 'Config.php'; ?>
<!DOCTYPE html>
<html>
    <head> 
        <title><?php echo APP_NAME ?></title> 
        <!--------------- Metatags ------------------->   
        <meta charset="utf-8" />
        <!-- Not allowing the user to zoom -->    
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1.0,maximum-scale=1.0, minimum-scale=1.0"/>
        <!-- iphone-related meta tags to allow the page to be bookmarked -->
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

        <!--------------- CSS files ------------------->    
        <!--<link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />-->        
<!--        <link rel="stylesheet" href="css/events.min.css" />-->
<!--        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile.structure-1.2.0.min.css" /> -->
        <link rel="stylesheet" href="css/my.css" />
        <link rel="stylesheet" href="css/jquery.ui.datepicker.mobile.css" />
        <style>

            .ui-page-theme-a .ui-btn.ui-btn-active, html .ui-bar-a .ui-btn.ui-btn-active, html .ui-body-a .ui-btn.ui-btn-active, html body .ui-group-theme-a .ui-btn.ui-btn-active, html head + body .ui-btn.ui-btn-a.ui-btn-active, .ui-page-theme-a .ui-checkbox-on:after, html .ui-bar-a .ui-checkbox-on:after, html .ui-body-a .ui-checkbox-on:after, html body .ui-group-theme-a .ui-checkbox-on:after, .ui-btn.ui-checkbox-on.ui-btn-a:after, .ui-page-theme-a .ui-flipswitch-active, html .ui-bar-a .ui-flipswitch-active, html .ui-body-a .ui-flipswitch-active, html body .ui-group-theme-a .ui-flipswitch-active, html body .ui-flipswitch.ui-bar-a.ui-flipswitch-active, .ui-page-theme-a .ui-slider-track .ui-btn-active, html .ui-bar-a .ui-slider-track .ui-btn-active, html .ui-body-a .ui-slider-track .ui-btn-active, html body .ui-group-theme-a .ui-slider-track .ui-btn-active, html body div.ui-slider-track.ui-body-a .ui-btn-active {
                background-color: <?php echo APP_DARKCOLOR ?> !important;
                border-color: <?php echo APP_DARKCOLOR ?>;
                color:#fff;
                text-shadow: 0 1px 0 #000;
            }

            .ui-page-theme-a .ui-btn, html .ui-bar-a .ui-btn, html .ui-body-a .ui-btn, html body .ui-group-theme-a .ui-btn, html head + body .ui-btn.ui-btn-a, .ui-page-theme-a .ui-btn:visited, html .ui-bar-a .ui-btn:visited, html .ui-body-a .ui-btn:visited, html body .ui-group-theme-a .ui-btn:visited, html head + body .ui-btn.ui-btn-a:visited {
                background-color: <?php echo APP_COLOR ?> !important;
                color:#fff !important;
                text-shadow: 0 1px 0 #000 !important;
            }

            .ui-page-theme-a .ui-radio-on:after, html .ui-bar-a .ui-radio-on:after, html .ui-body-a .ui-radio-on:after, html body .ui-group-theme-a .ui-radio-on:after, .ui-btn.ui-radio-on.ui-btn-a:after {
                border-color: <?php echo APP_DARKCOLOR ?> ;
            }
            .ui-page-theme-a .ui-btn:focus, html .ui-bar-a .ui-btn:focus, html .ui-body-a .ui-btn:focus, html body .ui-group-theme-a .ui-btn:focus, html head + body .ui-btn.ui-btn-a:focus, .ui-page-theme-a .ui-focus, html .ui-bar-a .ui-focus, html .ui-body-a .ui-focus, html body .ui-group-theme-a .ui-focus, html head + body .ui-btn-a.ui-focus, html head + body .ui-body-a.ui-focus {
                box-shadow: 0 0 12px  <?php echo APP_DARKCOLOR ?>;
            }
            .ui-page-theme-a .ui-btn:hover, html .ui-bar-a .ui-btn:hover, html .ui-body-a .ui-btn:hover, html body .ui-group-theme-a .ui-btn:hover, html head + body .ui-btn.ui-btn-a:hover {
                background-color:  <?php echo APP_DARKCOLOR ?> !important;
                border-color:  <?php echo APP_DARKCOLOR ?> ;
                color: #fff;
                text-shadow: 0 1px 0 #000;
            }

            #mapFilterList .ui-btn, #poiBubble .ui-btn ,  #mapFilterList .ui-btn, #poiBubble .ui-btn:hover,
            #cityFilterList .ui-btn, #poiBubble .ui-btn ,  #cityFilterList .ui-btn, #poiBubble .ui-btn:hover,
            #list .ui-btn, #poiBubble .ui-btn ,  #list .ui-btn, #poiBubble .ui-btn:hover
            {
                background-color: #f6f6f6 !important;
                color:#333!important;
                text-shadow:none!important;
                border-color: #ddd !important;
            }

            .list-scroll-container {
                 overflow: hidden;
                 /*overflow: auto;*/
                 background: <?php echo APP_DARKCOLOR ?>;
                 -webkit-border-radius: 12px;
                 -moz-border-radius: 12px;
                 border-radius: 12px;
            }
            #list form {
                 background: <?php echo APP_DARKCOLOR ?>;
            }

        </style>

        <!--------------- Javascript dependencies -------------------> 

        <!-- Google Maps JavaScript API v3 -->    
<!--        <script 
                src="https://maps.googleapis.com/maps/api/js?sensor=false">    
        </script>-->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

        <!-- Google Maps Utility Library - Infobubble -->     
        <script type="text/javascript"
                src = "http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble.js">
        </script>
        <!-- Overlapping markers Library: Deals with overlapping markers in Google Maps -->
        <script src="http://jawj.github.com/OverlappingMarkerSpiderfier/bin/oms.min.js"></script>  
        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>

        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.js"></script>


        <script>
            //reset type=date inputs to text
            $(document).bind("mobileinit", function() {
                $.mobile.page.prototype.options.degradeInputs.date = true;
            });
        </script>


        <script src="js/jQuery.ui.datepicker.js"></script>
        <script src="js/jquery.ui.datepicker.mobile.js"></script>	

        <!-- jQuery Mobile Library -->
<!--        <script src="js/jquery.mobile-1.2.0.min.js"></script>  -->
        <!-- Page params Library: Used to pass query params to embedded/internal pages of jQuery Mobile -->    
        <script src="js/jqm.page.params.js"></script>
        <!-- Template specific functions and handlers -->    
        <script src="js/events-lib.js"></script>   
        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-8323642-17']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();

        </script>

    </head> 

    <body>
        <!-- Home Page: Contains the Map -->
        <div data-role="page" id="page1" class="page">
<!--<a href="#popupMenu" data-rel="popup" data-transition="slideup" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-gear ui-btn-icon-left ui-btn-a">Actions...</a>-->

<div data-role="popup" id="popupMenu" data-theme="a">
        <ul data-role="listview" data-inset="true" style="min-width:210px;">
            <li data-role="list-divider">How do you want to get there?</li>
<!--            <li><a href="#">View details</a></li>-->
            <li><a  onclick="initStartingPoint('DRIVING')" ><img class="ui-li-icon" src='css/images/car-black.png' />Car</a></li>
            <li><a  onclick="initStartingPoint('WALKING')" ><img  class="ui-li-icon"  src='css/images/walk2-black.png' />Walk</a></li>
              <li><a  onclick="initStartingPoint('TRANSIT')"><img  class="ui-li-icon"  src='css/images/bus-black.png'  />Public transportation</a></li>
<!--            <li><a href="#">Edit</a></li>
            <li><a href="#">Disable</a></li>
            <li><a href="#">Delete</a></li>-->
        </ul>
</div>
            <div data-role="panel" id="myPanel"  data-display="overlay" >
                <div id="map-filter">
                    <div class="filters-list" id="mapFilterList">
                        <div class="checkboxWrapper" data-role="controlgroup" data-mini="true" data-theme="a">
                            dynamically filled with data 
                        </div>
                    </div>
                </div>
            </div>
            
            <div data-role="panel" id="transitPanel" data-position="right"  data-display="overlay" >
                <h3>Please select a transit option:</h3>
                  <div  data-role="controlgroup" data-type="horizontal" >  
                                          <a href="#page4"  data-role="button" data-icon="walk" data-iconpos="notext"  title="Search by Date">&nbsp;</a>
                    <!--  id="filter" -->    <a href="#myPanel"  data-role="button" data-icon="car" data-iconpos="notext" data-theme="a" title="Settings">&nbsp;</a>
                    <a href="#page4" data-role="button" data-icon="bus" data-iconpos="notext"  title="Search by Date">&nbsp;</a>

                </div>
               
            </div>
            
            <header data-tap-toggle="false" data-role="header"  data-id="constantNav"  >
                <!--                -->
                <!--                data-position="fixed"-->
                <!--                data-fullscreen="true"-->
                <!--                <span class="ui-title">Events</span>-->

                <!-- we use controlgroup so as to have two buttons, one for filters and one for date -->
                <div  data-role="controlgroup" data-type="horizontal" class="ui-btn-left" data-mini="true">  
                    <!--  id="filter" -->    <a href="#myPanel"  data-role="button" data-icon="filter" data-iconpos="notext" data-theme="a" title="Settings">&nbsp;</a>
                    <a href="#page4" id="datefilter" data-role="button" data-icon="calendar" data-iconpos="notext"  title="Search by Date">&nbsp;</a>
                </div>
                <!--  <div data-role="controlgroup" data-type='horizontal' class="ui-btn-right">
                                        <a href='index.html' data-role='button'  data-icon='arrow-u'>Walk</a>
                                        <a href='index.html' data-role='button'   data-icon='arrow-d' >Car</a>
                                        <a href='index.html' data-role='button'   data-icon='delete' >Transit</a>
                                </div>-->
                <h1><?php echo APP_NAME ?></h1>
                                <a href="#info" data-rel="dialog" data-icon="info" data-iconpos="notext" data-theme="a" title="Info" class="ui-btn-right">&nbsp;</a>
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="a">Near me</a></li>
                        <li><a href="#" class="pois-showall ui-btn-active" data-theme="a">Show all</a></li>
                        <li><a href="#page2" class="pois-list" data-theme="a">List</a></li>
                    </ul>
                </div><!-- /navbar -->
            </header>

            <!--            <div data-role="content" id="map-filter">
                            <div class="filters-list" id="mapFilterList">
                                <fieldset data-role="controlgroup" data-mini="true" data-theme="a">
                                     dynamically filled with data 
                                </fieldset>
                            </div>
                            <footer data-role="footer" data-poistion="fixed" data-fullscreen="true" class="filter-footer">
                                <a href="" id="apply" data-icon="gear" data-theme="a" title="Apply" class="ui-btn-right">Apply</a>
                            </footer>
                        </div>map-filter-->

            <div data-role="content" id="map-container">

                <!--                 <div id="warnings_panel" style="width:100%;height:10%;text-align:center"></div>-->

                <div id="map_canvas" class="map_canvas"></div>
            </div>
        </div>

        <!-- List Page: Contains a list with the results -->
        <div data-role="page" id="page2" class="page">

            <header data-role="header"  data-id="constantNav">
                <span class="ui-title">Events</span>
                <fieldset data-role="controlgroup" class="favourites-button" data-mini="true">
                    <input type="checkbox" name="favourites" id="favourites" class="custom" />
                    <label for="favourites">Favs</label>
                </fieldset>
                <a href="" data-icon="back" data-iconpos="notext" data-theme="a" title="Back" data-rel="back" class="ui-btn-right">&nbsp;</a>
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="a">Near me</a></li>
                        <li><a href="#" class="pois-showall" data-theme="a">Show all</a></li>
                        <li><a href="#page2" class="pois-list ui-btn-active" data-theme="a">List</a></li>
                    </ul>
                </div><!-- /navbar -->
            </header>

            <div class="list-container">
                <div class="list-scroll-container">
                    <div data-role="content" id="list" class="event">
                        <ul data-role='listview' data-filter='true' data-theme='a'>
                            <!-- dynamically filled with data -->
                        </ul>
                    </div><!--list-->
                </div><!--list-scroll-container-->
            </div><!--list-container-->
        </div><!-- /page -->

        <!-- Details Page: Contains the details of a selected element -->
        <div data-role="page" id="page3" data-title="Event fullstory page title" class="page">
            <header data-role="header"  data-fullscreen="true">
                <span class="ui-title">Events</span>
                <a href="" data-icon="back" data-iconpos="notext" data-theme="a" title="Back" data-rel="back" class="ui-btn-right">&nbsp;</a>

                <!--<div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="a">Near me</a></li>
                        <li><a href="#" class="pois-showall" data-theme="a">Show all</a></li>
                        <li><a href="#page2" class="pois-list" data-theme="a">List</a></li>
                    </ul>
                </div> /navbar --> 
            </header>


            <div class="list-container">
                <div class="list-scroll-container">
                    <div data-role="content">
                        <a onclick='seeOnMap(); return false;'  class="ui-btn ui-shadow ui-corner-all ui-icon-location ui-btn-icon-notext">See on map</a>
                        <span id="item"></span>
                        <!-- dynamically filled with data -->
                    </div><!--item-->
                </div><!--list-scroll-container-->
            </div><!--list-container-->

            <footer data-role="footer" data-tap-toggle="false" data-position="fixed" data-fullscreen="true">
                <a href="" id="addFav" data-icon="star" data-theme="a" title="Add to favourites" data-rel="star" class="ui-btn-center">Add to favourites</a>
                <a href="" id="removeFav" data-icon="star" data-theme="a" title="Remove from favourites" data-rel="star" class="ui-btn-center">Remove from favourites</a>
            </footer>

        </div><!-- /page -->


        <!-- Details Page: Contains the details of a selected element (calendar) -->
        <div data-role="page" id="page4" data-dialog="true" data-close-btn="none"  data-title="Events by Date" class="page">
            <header data-role="header"  data-fullscreen="true">
                <h1>Show Events after the selected Date</h1><a href="#" data-role="button" data-iconpos="notext" data-theme="a" title="Info" data-icon="back" data-rel="back"></a>
<!--                <a href="" data-icon="back" data-iconpos="notext" data-theme="a" title="Back" data-rel="back" class="ui-btn-right">&nbsp;</a>-->
            </header>

            <div data-role="content" id="itemDate">
<!--                class="filters-list"-->
<p id="datesHaveInvalidFormat"  style="display:none; font-style:italic; text-align:center;">Dates are not supported in the current dataset</p>
                <div>
             
                    <label for="useDate">Use Date Filter:</label>
    <input data-role="flipswitch" name="useDate" id="useDate" type="checkbox">
                    <p>Only the events starting after the selected date will be displayed</p>
<!--                    <label id="date" for="date">Search for Events:</label>-->
                    <input type="date" name="date" id="date" value="" />	
                </div>
            </div><!--item-->

            <footer data-role="footer" data-tap-toggle="false" data-position="fixed" data-fullscreen="true">
<a href=""  id="dateapply" data-icon="check" data-theme="a" title="Apply" class="ui-btn-center">Apply</a> 
            </footer>

        </div><!-- /page -->


        <!-- Info Page: Contains info of the currently used dataset -->  
        <div data-role="page" id="info">
            <header data-role="header">
                <span class="ui-title">Dataset Metadata</span>	
            </header>
            <article data-role="content">
                <ul data-role="listview">
                    <!-- dynamically filled with data -->
                </ul> 
            </article> 
        </div> 
        <!--<div data-role="panel" id="leftpanel1" data-position="left" data-display="overlay" data-theme="b">
        
                <h3>Left Panel: Reveal</h3>
                <p>This panel is positioned on the left with the reveal display mode. The panel markup is <em>after</em> the header, content and footer in the source order.</p>
                <p>To close, click off the panel, swipe left or right, hit the Esc key, or use the button below:</p>
                <a href="#demo-links" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-left ui-btn-inline">Close panel</a>
        
                </div>-->
        <script type="text/javascript">
            /****************** Global js vars ************************/

            /* GLobal map object */
            var map;
            /* List of pois read from json object */
            var pois = {};
            /* List of dataset metadata read from json object */
            var meta = {};
            /* Holds all markers */
            var markersArray = [];
            /* Define filters - get them from db */
            var filters = <?php
include_once CLASSES . 'filters.php';
printFilters();
?>;
            /* Remember if a page has been opened at least once */
            var lastLoaded = '';
            /* Remember if 'near me' marker is loaded */
            var isNearMeMarkerLoaded = false;
            /* 
             * Remember if map was initialy loaded. If not
             * it means we loaded list page
             */
            var isMapLoaded = false;
            /*
             * Keeps page id to emulate full url using querystring
             */
            var pageId = 0;
            /* Set infoBubble global variable */
            var infoBubble;

            var dateIsNull = true;

            /* The coordinates of the center of the map */
            //Issy
            var mapLat = <?php echo MAP_CENTER_LATITUDE; ?>;
            var mapLon = <?php echo MAP_CENTER_LONGITUDE; ?>;
            var mapZoom = <?php echo MAP_ZOOM; ?>;

            /* The url of the dataset */
            var datasetUrl = "<?php echo DATASET_URL; ?>";

            /* Just call the initialization function when the page loads */
            $(window).load(function() {
                globalInit();
            });

        </script>
    </body>
</html>