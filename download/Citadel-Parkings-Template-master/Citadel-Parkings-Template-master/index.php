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
<!--        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />-->
        <link rel="stylesheet" href="css/my.css" />
                    <style>

            .ui-btn.ui-btn-active {
                background-color: <?php echo APP_DARKCOLOR ?> !important;
                border-color: <?php echo APP_DARKCOLOR ?> !important;
                color:#fff;
                text-shadow: 0 1px 0 #000;
            }

            
            .ui-btn {
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
        <script type="text/javascript"
                src="https://maps.googleapis.com/maps/api/js?sensor=false">
        </script>
        <!-- Google Maps Utility Library - Infobubble -->     
        <script type="text/javascript"
                src = "http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble.js">
        </script>
        <!-- Overlapping markers Library: Deals with overlapping markers in Google Maps -->
        <script src="http://jawj.github.com/OverlappingMarkerSpiderfier/bin/oms.min.js"></script>  
        <!-- jQuery Library --> 
        <script src="js/jquery-1.8.2.min.js"></script>
        <!-- jQuery Mobile Library -->
<!--        <script src="js/jquery.mobile-1.2.0.min.js"></script>  -->
                   <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.js"></script>
        <!-- Page params Library: Used to pass query params to embedded/internal pages of jQuery Mobile -->    
        <script src="js/jqm.page.params.js"></script>
        <!-- Template specific functions and handlers -->    
        <script src="js/parking-lib.js"></script>   
         
    </head> 

    <body>
        <!-- Home Page: Contains the Map -->
        <div data-role="page" id="page1" data-theme="b" class="page">
            <div data-role="popup" id="popupMenu" data-theme="a">
        <ul data-role="listview" data-inset="true" style="min-width:210px;">
            <li data-role="list-divider">How do you want to get there?</li>
            <li><a  onclick="initStartingPoint('DRIVING')" ><img class="ui-li-icon" src='css/images/car-black.png' />Car</a></li>
            <li><a  onclick="initStartingPoint('WALKING')" ><img  class="ui-li-icon"  src='css/images/walk2-black.png' />Walk</a></li>
              <li><a  onclick="initStartingPoint('TRANSIT')"><img  class="ui-li-icon"  src='css/images/bus-black.png'  />Public transportation</a></li>
        </ul>
</div>
            <header data-role="header" data-theme="b" data-id="constantNav" data-fullscreen="true">
                <a href="#info" data-rel="dialog" data-icon="info" data-iconpos="notext" data-theme="b" title="Info">&nbsp;</a>
                <span class="ui-title"><?php echo APP_NAME ?></span>
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="b">Near me</a></li>
                        <li><a href="#" class="pois-showall ui-btn-active" data-theme="b">Show all</a></li>
                        <li><a href="#page2" class="pois-list" data-theme="b">List</a></li>
                    </ul>
                </div><!-- /navbar -->
            </header>
 <!--data-role="content"-->
            <div role="main" class="ui-content" id="map-container">
                <div id="map_canvas" class="map_canvas" style=" top:0; position:absolute;"></div>
            </div>
        </div>       

        <!-- List Page: Contains a list with the results -->
        <div data-role="page" data-theme="b" id="page2" class="page">

            <header data-role="header" data-posistion="fixed" data-id="constantNav">
                <a href="#info" data-rel="dialog" data-icon="info" data-iconpos="notext" data-theme="b" title="Info">&nbsp;</a>
                <span class="ui-title"> Find Parking Lots List </span>
                <a href="" data-icon="back" data-iconpos="notext" data-theme="b" title="Back" data-rel="back">&nbsp;</a>
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="b">Near me</a></li>
                        <li><a href="#" class="pois-showall" data-theme="b">Show all</a></li>
                        <li><a href="#page2" class="pois-list ui-btn-active" data-theme="b">List</a></li>
                    </ul>
                </div><!-- /navbar -->
            </header>
                
            <div class="list-container">
                <div class="list-scroll-container">
                    <div data-role="content" id="list" class="parking">
                        <ul data-role='listview' data-filter='true' data-theme='b'>
                        <!-- dynamically filled with data -->
                        </ul>
                    </div><!--list-->
                </div><!--list-scroll-container-->
            </div><!--list-container-->
        </div><!-- /page -->
        
        <!-- Details Page: Contains the details of a selected element -->
        <div data-role="page" data-theme="b" id="page3" data-title="Parking Details" class="page">
            <header data-role="header" data-posistion="fixed" data-fullscreen="true">
                <a href="#info" data-rel="dialog" data-icon="info" data-iconpos="notext" data-theme="b" title="Info">&nbsp;</a>
                <span class="ui-title">Parking details</span>
                <a href="" data-icon="back" data-iconpos="notext" data-theme="b" title="Back" data-rel="back">&nbsp;</a>
                <div data-role="navbar" class="navbar">
                    <ul>
                        <li><a href="#" class="pois-nearme" data-theme="b">Near me</a></li>
                        <li><a href="#" class="pois-showall" data-theme="b">Show all</a></li>
                        <li><a href="#page2" class="pois-list" data-theme="b">List</a></li>
                    </ul>
                </div><!-- /navbar --> 
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
        </div><!-- /page -->
            
        <!-- Info Page: Contains info of the currently used dataset -->  
        <div data-role="page" id="info" data-theme="b">
            <header data-role="header">
                <span class="ui-title">Metadata Information</span>	
            </header>
            <article data-role="content">
                <ul data-role="listview">
                    <!-- dynamically filled with data -->
                </ul> 
            </article> 
        </div>    
            

        <script type="text/javascript">
                    /****************** Global js vars ************************/
                    
                    /* GLobal map object */
                    var map;
                    /* List of pois read from json object */
                    var pois = {};
                    /* List of dataset metadata read from json object */
                    var meta = {};
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
                    
                    /* The coordinates of the center of the map */
                    
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