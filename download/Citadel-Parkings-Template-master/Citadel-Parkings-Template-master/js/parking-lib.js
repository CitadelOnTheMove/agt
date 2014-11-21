/****************** Functions *****************************/

var currentPoi;
var markersArray;
var stepDisplay;
/* Initialization function.
 * It is called once when the page is load
 */
function globalInit() {
    getPoisFromDataset(function(pois) {
        initializeMap(mapLat, mapLon);
        loadListPageData();
        refreshListPageView();
        loadDetailsPage();
        loadInfoPage();
    });
}

/* Returns an array of poi objects.
 * The poi object contains the data described in the 
 * Citadel Common POI format
 */
function getPoisFromDataset(resultCallback)
{
    $.getJSON(datasetUrl, function(data) {
        meta['id'] = data.dataset.identifier;
        meta['updated'] = data.dataset.updated;
        meta['created'] = data.dataset.created;
        meta['lang'] = data.dataset.lang;
        meta['author_id'] = data.dataset.author.id;
        meta['author_value'] = data.dataset.author.value;
        $.each(data.dataset.poi, function(i, poi) {
            pois[poi.id] = poi;
        });
        resultCallback(pois);
    });
}

/* Initialises a google map using map api v3 */
function initializeMap(Lat, Lon) {

    // Instantiate an info window to hold step text.
    stepDisplay = new google.maps.InfoWindow();
    //define the center location 
    var myLatlng = new google.maps.LatLng(Lat, Lon);
    //define the map options
    var mapOptions = {
        center: myLatlng,
        zoom: mapZoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    //instantiate the map wih the options and put it in the div holder, "map-canvas"
    map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
    addMarkers();

    var rendererOptions = {
        map: map
    }
    directionsService = new google.maps.DirectionsService();
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
}

/* Adds all the markers on the global map object */
function addMarkers()
{
    markersArray = new Array();
    /* var oms will hold all the markers so as to resolve
     * the issue of overlapping ones
     */


    var oms = new OverlappingMarkerSpiderfier(map);
    var iw = new google.maps.InfoWindow();
    oms.addListener('click', function(marker) {
        iw.setContent(marker.desc);
        iw.open(map, marker);
        currentTransit.lat = marker.position.lat();
        currentTransit.lon = marker.position.lng();
    });

    /* We initialize the infobubble styling */
    infoBubble = new InfoBubble({
        shadowStyle: 1,
        padding: 0,
        backgroundColor: '#00467A',
        borderRadius: 10,
        arrowSize: 10,
        borderWidth: 1,
        maxWidth: 300,
        borderColor: '#6AA7D2',
        disableAutoPan: false,
        arrowPosition: 30,
        arrowStyle: 2,
        hideCloseButton: true
    });

    /* For every POI we add a marker with an attached infoBubble */
    $.each(pois, function(i, poi) {
        /*  posList contains a list of space separated coordinates.
         *  The first two are lat and lon
         */
        var coords = poi.location.point.pos.posList.split(" ");
        var current_markerpos = new google.maps.LatLng(coords[0], coords[1]);
        var marker_image = getMarkerImage(poi);
        var current_marker = new google.maps.Marker({
            position: current_markerpos,
            map: map,
            icon: marker_image
        });
        current_marker.citadelPoi = poi;
        oms.addMarker(current_marker);
        markersArray[poi.id] = current_marker;
        console.log("markersArray", markersArray);
        google.maps.event.addListener(current_marker, 'click', function() {
            infoBubble.setContent(setInfoWindowParkingPoi(this.citadelPoi));
            infoBubble.open(map, this);
            setTimeout("$('#parkingBubble').parent().parent().css('overflow', 'hidden')", 100);

        });
    });
}

/*
 * Returns the marker image based on parking type
 */
function getMarkerImage(poi, template) {
    var marker_image = template == "list" ? "images/parking-list.png" : "images/parking.png";
    if (getCitadel_attr(poi, "#Citadel_parkType").text == 'underground')
        marker_image = template == "list" ? "images/parking-underground-list.png" : "images/parking-underground.png";

    return marker_image;
}


/* Sets the content of the infoBubble for the given 
 * parking lot
 */
function setInfoWindowParkingPoi(poi)
{
    var capacity = "";
    console.log("setting current POI, setInfoWindow", poi);
    currentPoi = poi;
    //alert(currentPoi.id  +"setInfo");
    /* Get the Parking specific attributes of the POI */
    if (getCitadel_attr(poi, "#Citadel_parkCapacity").term != null && getCitadel_attr(poi, "#Citadel_parkCapacity").term != '') {
        if (getCitadel_attr(poi, "#Citadel_parkSpaces").term != null && getCitadel_attr(poi, "#Citadel_parkSpaces").term != '') {

            capacity = "<div class='capacity'>" +
                    getCitadel_attr(poi, "#Citadel_parkSpaces").text +
                    " of " +
                    +getCitadel_attr(poi, "#Citadel_parkCapacity").text + " slots available</div>";
        }
        else {
            capacity = "<div class='capacity'>" +
                    getCitadel_attr(poi, "#Citadel_parkCapacity").term +
                    ":" + getCitadel_attr(poi, "#Citadel_parkCapacity").text +
                    "</div>";
        }
    }

    var contentTemplate =
            "<div id='parkingBubble'><a href='#page3' onclick='overrideDetailClick(\"" + poi.id + "\"); return false;'>" +
            "<div class='title'>" +
            poi.title +
            "</div>" +
            "<div class='address'>" + poi.location.address.value +
            "</div>" + capacity +
            "</a><a data-rel='popup' onclick='showTransitOptions()'  data-transition='slideup' class='ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-navigation ui-btn-icon-left ui-btn-a transitPopup'>Take me there</a></div><div id='bubbleClose'><a href='' onclick='return overrideBubbleCloseClick();'><img src='images/close.png' width='25' height='25' alt='close' /></a></div>";

    return contentTemplate;
}

/* Sets the content of the details page for the given 
 * parking lot
 */
function setDetailPageParkingPoi(poi)
{
    /* Get the Parking specific attributes of the POI */
    var telephone = getCitadel_attr(poi, "#Citadel_telephone").text;
    var website = getCitadel_attr(poi, "#Citadel_website").text;
    var email = getCitadel_attr(poi, "#Citadel_email").text;
    var parkType = getCitadel_attr(poi, "#Citadel_parkType").text;
    var parkFloors = getCitadel_attr(poi, "#Citadel_parkFloors").text;
    var parkCapacity = getCitadel_attr(poi, "#Citadel_parkCapacity").text;
    var parkSpaces = getCitadel_attr(poi, "#Citadel_parkSpaces").text;
    var openHours = getCitadel_attr(poi, "#Citadel_openHours").text;
    var nearTransport = getCitadel_attr(poi, "#Citadel_nearTransport").text;
    var otherAttributes = getCitadel_attrs(poi, "");

    var contentTemplate =
            "<h1>" + poi.title + "</h1>" +
            "<div class='event-data'>" +
            "<ul>";

    /* Display the Parking specific attributes of the POI if they exist */
    if (telephone)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-phone.png' alt='Telephone' /></span><span class='image-text'><a href='tel:" + telephone + "'>" + telephone + "</a></span></li>";
    if (website)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-website.png' alt='Website' /></span><span class='image-text'><a href='" + website + "' target='_blank'>" + website + "</a></span></li>";
    if (email)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-email.png' alt='Email' /></span><span class='image-text'><a href='mailto:" + email + "'>" + email + "</a></span></li>";
    if (parkType)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-parking.png' alt='Parking' /></span><span class='image-text'>" + parkType + "</span></li>";
    if (parkCapacity)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-slots.png' alt='Slots' /></span><span class='image-text'>" + parkCapacity + " slots</span></li>";
    if (parkSpaces)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-spaces.png' alt='Spaces' /></span><span class='image-text'>" + parkSpaces + " available</span></li>";
    if (openHours)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-openhours.png' alt='Open hours' /></span><span class='image-text'>" + openHours + "</span></li>";
    if (nearTransport)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-transportation.png' alt='Transportation' /></span><span class='image-text'>" + nearTransport + "</span></li>";
    if (parkFloors)
        contentTemplate += "<li><span>" + getCitadel_attr(poi, "#Citadel_parkFloors").term + "</span>" + parkFloors + "</li>";

    /* Display the rest attributes of the POI */
    for (i = 0; i < otherAttributes.length; i++) {
        contentTemplate += "<li><span>" + otherAttributes[i].term + "</span>" + otherAttributes[i].text + "</li>";
    }

    contentTemplate += "</ul>" +
            "</div>";

    return contentTemplate;
}

/* Sets the content of the Listing Page         */
function setListPageParkingPois()
{
    var contentTemplate = "";

    $.each(pois, function(i, poi) {
        var parkType = getCitadel_attr(poi, "#Citadel_parkType").text;
        var parkCapacity = getCitadel_attr(poi, "#Citadel_parkCapacity").text;
        var parkSpaces = getCitadel_attr(poi, "#Citadel_parkSpaces").text;
        var capacity = "";
        if (getCitadel_attr(poi, "#Citadel_parkCapacity").term != '') {
            capacity = "<p>" +
                    getCitadel_attr(poi, "#Citadel_parkCapacity").text +
                    " slots" +
                    "</p>";
        }

        var spaces = "";
        if (getCitadel_attr(poi, "#Citadel_parkSpaces").term != '') {
            spaces = "<p>" +
                    getCitadel_attr(poi, "#Citadel_parkSpaces").text +
                    " available" +
                    "</p>";
        }
        contentTemplate +=
                "<li>" +
                "<a href='' onclick='overrideDetailClick(\"" + poi.id + "\"); return false;'>" +
                "<img src='" + getMarkerImage(poi, 'list') + "' alt='Parking' />" +
                "<div>" + poi.title + "</div>" +
                capacity + spaces +
                "</a>" +
                "</li>";
    });

    return contentTemplate;
}

/* Sets the content of the info page 
 * based on dataset metadata
 */
function setInfoPage()
{
    var contentTemplate =
            "<li>Dataset ID: <b>" + meta.id + "</b></li>" +
            "<li>Created at: <b><time>" + meta.created + "</time></b></li>" +
            "<li>Updated at: <b><time>" + meta.updated + "</time></b></li>" +
            "<li>Language: <b>" + meta.lang + "</b></li>" +
            "<li>Author ID: <b>" + meta.author_id + "</b></li>" +
            "<li>Author Value: <b>" + meta.author_value + "</b></li>" +
            "<li>Created by: <b><a href='http://www.atc.gr' target='_blank'>atc.gr</a></b></li>";

    return contentTemplate;
}

/*  Returns the attribute with the given tplIdentifer
 *  or an empty object if there is no such an atttibute
 *  Expected values for the tplIdentifer are:
 * 
 *  #Citadel_telephone                 
 *  #Citadel_website                                      
 *  #Citadel_email                                                           
 *  #Citadel_parkType                                                                                
 *  #Citadel_parkFloors                                                                                                      
 *  #Citadel_parkCapacity  
 *  #Citadel_parkSpaces                                                                                                                        
 *  #Citadel_image                                                                                                                                               
 *  #Citadel_eventStart                                                                                                                                                                    
 *  #Citadel_eventEnd
 *  #Citadel_eventPlace
 *  #Citadel_eventDuration 
 *  #Citadel_openHours 
 *  #Citadel_nearTransport                                                                                                                                                                                                                                                                                                                                                                                               
 */
function getCitadel_attr(poi, tplIdentifer) {
    var attribute = {
        "term": "",
        "type": "",
        "text": ""
    };
    if (poi)
    {
        $.each(poi.attribute, function(i, attr) {
            if (attr.tplIdentifier === tplIdentifer)
            {
                attribute = {
                    "term": attr.term,
                    "type": attr.type,
                    "text": attr.text
                };
                return false;
            }
        });
        return attribute;
    }
    else {
        return "";
    }
}

/*  Returns an array of all the attributes with 
 *  the given tplIdentifer.    
 */
function getCitadel_attrs(poi, tplIdentifer) {
    var attributes = new Array();
    $.each(poi.attribute, function(i, attr) {
        if (attr.tplIdentifier === tplIdentifer)
        {
            attribute = {
                "term": attr.term,
                "type": attr.type,
                "text": attr.text
            };
            attributes.push(attribute);
        }
    });
    return attributes;
}

/* Bubble on click event listener */
function overrideDetailClick(id) {
    //Get poi by id
    var poi = pois[id];
    currentPoi = poi;
    console.log("pois[" + id + "]", poi);
    //Pass data to details constructor
    $('#item').html(setDetailPageParkingPoi(poi));

    $.mobile.changePage("#page3", {transition: "none", reverse: false, changeHash: false});
    window.location.href = "#page3?id=" + id;

    return true;
}

/* Bubble close click event listener */
function overrideBubbleCloseClick() {
    infoBubble.close();
    return false;
}

/* Load list page using pois variable */
function loadListPageData() {
    $('#list > ul').html(setListPageParkingPois());
}

/* Refreshes the list of POIS in the List Page */
function refreshListPageView() {
    if ($("#list > ul").hasClass("ui-listview")) {
        $("#list > ul").listview('refresh');
    }
}

/* Refreshes the global map onject */
function refreshMap() {
    if (map) {
        google.maps.event.trigger(map, 'resize');
    }
}

/* Loads the Details Page of a POI */
function loadDetailsPage() {
    /* pageId equals 0 when the Home Page or the List page 
     * is active
     */
    if (pageId != 0) {
        $('#item').html(setDetailPageParkingPoi(pois[pageId]));
        pageId = 0;
    }
}

/* Loads the Metadata Info Page */
function loadInfoPage() {
    $('#info > article > ul').html(setInfoPage());
}

/****************** Event Handlers*************************/

$(document).ready(function() {

    /* Click handler for the 'near me' button */
    $('.pois-nearme').click(function() {
        lastLoaded = 'nearme';
        $.mobile.changePage("#page1", {transition: "none"});
        $('.navbar > ul > li > a').removeClass('ui-btn-active');
        $('.pois-nearme').addClass('ui-btn-active');

        /* Check if we can get geolocation from the browser */
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var myLatlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                //define the map options
                var mapOptions = {
                    center: myLatlng,
                    zoom: mapZoom,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map.setOptions(mapOptions);

                /* Load near me marker only once */
                isNearMeMarkerLoaded = true;
                var currentmarkerpos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

                var currentmarker = new google.maps.Marker({
                    position: currentmarkerpos,
                    map: map
                });
            });
        } else {
            initializeMap(mapLat, mapLon);
            google.maps.event.trigger(map, 'resize');
        }

        /* Event Tracking in Analytics */
        //trackEvent('nearme');
    });

    /* Click handler for the 'show all' button */
    $('.pois-showall').click(function() {
        if (lastLoaded != 'showall') {
            lastLoaded = 'showall';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    //initializeMap(mapLat, mapLon);
                    var myLatlng = new google.maps.LatLng(mapLat, mapLon);
                    //define the map options
                    var mapOptions = {
                        center: myLatlng,
                        zoom: mapZoom,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    map.setOptions(mapOptions);
                });
            } else {
                initializeMap(mapLat, mapLon);
                google.maps.event.trigger(map, 'resize');
            }
        }

        $.mobile.changePage("#page1", {transition: "none"});
        $('.navbar > ul > li > a').removeClass('ui-btn-active');
        $('.pois-showall').addClass('ui-btn-active');

        /* Event Tracking in Analytics */
    });

    /* Click handler for the 'list' button */
    $('.pois-list').click(function() {
        $.mobile.changePage("#page2", {transition: "none"});
        /* Event Tracking in Analytics */
    });

    /* Checks for the active page and performs the 
     * relevant actions
     */
    $('.page').bind("pageshow", function(event, data) {
        if ($(this).attr('id') == 'page1') {
            refreshMap();
            pageId = 0;
        }
        if ($(this).attr('id') == 'page2') {
            $('.navbar > ul > li > a').removeClass('ui-btn-active');
            $('.pois-list').addClass('ui-btn-active');
            refreshListPageView();
            pageId = 0;
        }
        if ($(this).attr('id') == 'page3') {
            $('.navbar > ul > li > a').removeClass('ui-btn-active');
        }
    });
});

/* Refreshes the map when the mobile device
 *  orientation changes  
 */
$(window).resize(function() {
    refreshMap();
});

/* Used to hide the nav bar in iphone  */
window.addEventListener("load", function() {
    setTimeout(function() {
        window.scrollTo(0, 1);
    }, 0);
});
/*
 * Event Tracking in Google Analytics 
 *  Category is the same for all events 
 *  City-Name is the same for all the events
 */
//function trackEvent(action) {     
//    _gaq.push(['_trackEvent', 'Parking', action, 'City-Name']);
//}


function Transit(lat, lon, type) {
    this.lat = lat;
    this.lon = lon;
    this.type = type;
}

var currentTransit = new Transit("", "", "");
var directionsService;
var markerArray = [];
var directionsDisplay;
function initStartingPoint(transitType)
{
    $.mobile.loading("show", {
        text: 'Loading your route',
        textVisible: true
    });
    $("#popupMenu").popup("close");
    infoBubble.close();
    var startPoint;


    /* Check if we can get geolocation from the browser */
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            $.mobile.loading("show", {
                text: 'Loading your route',
                textVisible: true
            });

            /* Load near me marker only once */
            isNearMeMarkerLoaded = true;
            var currentmarkerpos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

            var currentmarker = new google.maps.Marker({
                position: currentmarkerpos,
                map: map,
                animation: google.maps.Animation.DROP
            });
            startPoint = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            // Route the directions and pass the response to a
            // function to create markers for each step.
            calcRoute(startPoint, transitType);
        }, function(error) {

            startPoint = new google.maps.LatLng(mapLat, mapLon);
            calcRoute(startPoint, transitType);
        });
    }

    else
    {
        startPoint = new google.maps.LatLng(mapLat, mapLon);
        calcRoute(startPoint, transitType);
    }
}

function calcRoute(startPoint, transitType) {
    console.log("start", startPoint);

    endPoint = new google.maps.LatLng(currentTransit.lat, currentTransit.lon);
    console.log("end", endPoint);
    var request = {
        origin: startPoint,
        destination: endPoint,
        travelMode: google.maps.TravelMode[transitType]
    };
    directionsService.route(request, function(response, status) {

        if (status == google.maps.DirectionsStatus.OK) {
            //    var warnings = document.getElementById('warnings_panel');
            //warnings.innerHTML = '<b>' + response.routes[0].warnings + '</b>';
            //   console.log("Routes", response.routes);
            directionsDisplay.setDirections(response);
            showSteps(response);
            $.mobile.loading("hide");
        }
        else {
            alert("Could not create route: " + status);
        }
    });

    // First, remove any existing markers from the map.
    for (var i = 0; i < markerArray.length; i++) {
        markerArray[i].setMap(null);
    }
    // Now, clear the array itself.
    markerArray = [];
}

function showSteps(directionResult) {
    // For each step, place a marker, and add the text to the marker's
    // info window. Also attach the marker to an array so we
    // can keep track of it and remove it when calculating new
    // routes.
    var myRoute = directionResult.routes[0].legs[0];

    for (var i = 0; i < myRoute.steps.length; i++) {
        var marker = new google.maps.Marker({
            position: myRoute.steps[i].start_location,
            map: map,
            animation: google.maps.Animation.DROP
        });
        attachInstructionText(marker, myRoute.steps[i].instructions);
        markerArray[i] = marker;
    }
}

function attachInstructionText(marker, text) {
    google.maps.event.addListener(marker, 'click', function() {
        // Open an info window when the marker is clicked on,
        // containing the text of the step.
        stepDisplay.setContent(text);
        stepDisplay.open(map, marker);
        //   fixMapHeight();
    });
}

/*see the selected sensor on the map*/
function seeOnMap()
{
    $.mobile.changePage("#page1", {transition: "none"});
    map.setZoom(16);
    infoBubble.setContent(setInfoWindowParkingPoi(currentPoi));
    infoBubble.open(map, markersArray[currentPoi.id]);
}

function showTransitOptions()
{
    $("#popupMenu").popup("open");
}