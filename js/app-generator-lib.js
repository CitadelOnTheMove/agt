/*****************Global variables********************/
var newMarker = null;
var point = null;
var geocoder;
var currentPoiId;
var myLatlng;

/****************** Functions *****************************/

/* Initialization function.
 * It is called once when the page is load
 */
function globalInit() {
    getPoisFromDataset(function(pois) {
        setFilters();
        setCityFilters();
        hideAddressBar();
        setTimeout(function() {
            fixMapHeight();
            initializeMap(mapLat, mapLon);
        }, 500);
        loadListPageData();
        refreshListPageView();
        loadDetailsPage();
        loadInfoPage();
    });

    //Enable scroll for older versions of android
    touchScroll('mapFilterList');
}

/* Returns an array of poi objects.
 * The poi object contains the data described in the 
 * Citadel Common POI format
 */
function getPoisFromDataset(resultCallback)
{
    $.getJSON(datasetUrl, function(data) {

        pois = new Array();
        var k = 0;

        $.each(data, function(i, datasetObject) {
            console.log("i = " + i + " datasetObject = " + datasetObject);
            console.log(datasetObject.dataset.identifier);

            meta['id'] = datasetObject.dataset.identifier;
            meta['updated'] = datasetObject.dataset.updated;
            meta['created'] = datasetObject.dataset.created;
            meta['lang'] = datasetObject.dataset.lang;
            meta['author_id'] = datasetObject.dataset.author.id;
            meta['author_value'] = datasetObject.dataset.author.value;
            $('.ui-title').html(datasetObject.appName);

            $.each(datasetObject.dataset.poi, function(j, poi) {
                poi.id = k;
                k++;
                pois[poi.id] = poi;            
            }); 
        });

        resultCallback(pois);
    });
}

/* Initialises a google map using map api v3 */
function initializeMap() {
    var customZoom = 0;
    var geolocationSuccessful = false;
    var mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };
    //instantiate the map wih the options and put it in the div holder, "map-canvas"
    map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
    if (navigator.geolocation) {
        console.log('geolocation supported');
        navigator.geolocation.getCurrentPosition(function(position) {

            geolocationSuccessful = true;
            myLatlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            var currentMarker = new google.maps.Marker({
                position: myLatlng,
                map: map
            });
            //define the map options
            customZoom = 12;
            mapOptions = {
                center: myLatlng
               // zoom: mapZoom + 10
            };
        });
    }

    if (!geolocationSuccessful)
    {
        customZoom = 2;
        myLatlng = new google.maps.LatLng(mapLat, mapLon);
        mapOptions = {
            center: myLatlng,
            //zoom: mapZoom
        };

    }
    mapOptions = {
        center: myLatlng,
        zoom: customZoom
    };

    map.setOptions(mapOptions);
    map.setCenter(myLatlng);
    addMarkers();
}

/* Adds all the markers on the global map object */
function addMarkers()
{
    for (var i = 0; i < markersArray.length; i++) {
        markersArray[i].setMap(null);
    }
    markersArray = new Array();
    if (infoBubble)
        overrideBubbleCloseClick();

    /* var oms will hold all the markers so as to resolve
     * the issue of overlapping ones
     */
    var oms = new OverlappingMarkerSpiderfier(map);
    var iw = new google.maps.InfoWindow();
    oms.addListener('click', function(marker) {
        iw.setContent(marker.desc);
        iw.open(map, marker);
    });

    /* We initialize the infobubble styling */
    infoBubble = new InfoBubble({
        shadowStyle: 1,
        padding: 5,
        backgroundColor: bubbleColor,
        borderRadius: 10,
        arrowSize: 10,
        borderWidth: 2,
        maxWidth: 300,
        maxHeight: 400,
        minHeight: 150,
        borderColor: '#3A3A3A',
        disableAutoPan: false,
        arrowPosition: 30,
        arrowStyle: 2,
        hideCloseButton: true
    });

    /* For every POI we add a marker with an attached infoBubble */
    $.each(pois, function(i, poi) {
        if (isFilterSelected(poi.category)) {
            /*  posList contains a list of space separated coordinates.
             *  The first two are lat and lon
             */
            var coords = poi.location.point.pos.posList.split(" ");
            var current_markerpos = new google.maps.LatLng(coords[0], coords[1]);
            var marker_image = getFavouriteValue(poi.id) ? "images/star.png" : getMarkerImage(poi.category[0]);
            var current_marker = new google.maps.Marker({
                position: current_markerpos,
                map: map,
                icon: marker_image
            });

            markersArray.push(current_marker);
            oms.addMarker(current_marker);
            google.maps.event.addListener(current_marker, 'click', function() {
                infoBubble.setContent(setInfoWindowPoi(poi));
                infoBubble.open(map, current_marker);

                setTimeout("$('#poiBubble').parent().parent().css('overflow', 'hidden')", 100);
            });
        }
    });
}

/*
 * Returns the marker image picking a unique color for every category
 */

function getMarkerImage(category) {
    var coloredMarkers = new Array();
    var marker_parking;

    if (category == "Parking") {
        marker_parking = "images/parking.png";
        return marker_parking;
    }
    else {
        for (var j = 0; j < 17; j++) {
            coloredMarkers[j] = 'images/pin' + j + '.png';
        }

        for (i = 0; i < filters.length; i++) {
            if (filters[i].name == category) {
                return coloredMarkers[i % 16];
            }
        }
    }
}


function getMarkerClass(category) {

    var coloredClassMarkers = new Array();

    var marker_parking;

    if (category == "Parking") {
        marker_parking = 'pinParking';
        return marker_parking;
    }
    else {
        for (var j = 0; j < 17; j++) {
            coloredClassMarkers[j] = 'pin' + j;
        }

        for (i = 0; i < filters.length; i++) {
            if (filters[i].name == category) {
                return coloredClassMarkers[i % 16];
            }
        }
    }
}

function refreshBubblePoiVotes(poiId)
{
    $.ajax({
        type: "GET",
        url: getPoiVotesScript + "?poiId=" + poiId,
        cache: false,
        success: onRefreshBubblePoiVotesSuccess,
        error: onRefreshBubblePoiVotesFailure
    });
}
/* Sets the content of the infoBubble for the given 
 * poi
 */
function setInfoWindowPoi(poi)
{
    refreshBubblePoiVotes(poi.id);
    var category = "";

    /* Get the Event specific attributes of the POI */
    if (poi.category.length > 0) {
        category = "<div class='category'>" +
                poi.category.join(', ') +
                "</div>";
    }

    var contentTemplate =
            "<div id='poiBubble'><a href='#page3' onclick='overrideDetailClick(\"" + poi.id + "\"); return false;'>" +
            "<div class='title'>" +
            poi.title +
            "</div>" + "<div class='address'>" + poi.location.address.value + "</div>\n" +
            "<span class='bubbleUpVoteWrapper'><img src='images/like-32.png'/><span id='bubbleUpVotes'></span></span><span  class='bubbleDownVoteWrapper'><img src='images/dislike-32.png'/><span id='bubbleDownVotes'></span></span>" +
            "</a></div><div id='bubbleClose'><a href='' onclick='return overrideBubbleCloseClick();'><img src='images/close.png' width='25' height='25' alt='close' /></a></div>";

    return contentTemplate;
}

function resetVoteLoader()
{
    $('#downVoteScore').html("<img  src='images/loader.GIF'  />");
    $('#upVoteScore').html("<img  src='images/loader.GIF'  />");
    //$('#voteScore').html("<img  src='images/loader.GIF'  />");
}
/* Sets the content of the details page for the given 
 * poi
 */
function setDetailPagePoi(poi)
{
    resetVoteLoader();
    currentPoiId = poi.id;
    /* Get the Event specific attributes of the POI */
    var image = getCitadel_attr(poi, "#Citadel_image").text;
    var telephone = getCitadel_attr(poi, "#Citadel_telephone").text;
    var website = getCitadel_attr(poi, "#Citadel_website").text;
    var email = getCitadel_attr(poi, "#Citadel_email").text;
    var openHours = getCitadel_attr(poi, "#Citadel_openHours").text;
    var nearTransport = getCitadel_attr(poi, "#Citadel_nearTransport").text;
    var eventStart = getCitadel_attr(poi, "#Citadel_eventStart").text;
    var eventEnd = getCitadel_attr(poi, "#Citadel_eventEnd").text;
    var eventPlace = getCitadel_attr(poi, "#Citadel_eventPlace").text;
    var eventDuration = getCitadel_attr(poi, "#Citadel_eventDuration").text;
    var otherAttributes = getCitadel_attrs(poi, "");

    var contentTemplate =
            "<div class='poi-data'>" +
            "<ul>";

    if (image)
        contentTemplate += "<li class='image'><img src='" + image + "' alt='Event image' /></li>";

    contentTemplate += "<li><h1>" + poi.title + "</h1></li>";
    if (poi.description) {
        contentTemplate += "<li>" + poi.description + "</li>";
    }
    if (poi.location.address.value) {
        contentTemplate += "<li>" + poi.location.address.value + " " + poi.location.address.postal + "</li>";
    }
    if (poi.category) {
        contentTemplate += "<li>" + poi.category + "</li>";
    }

    /* Display the Event specific attributes of the POI if they exist */
    if (telephone)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-phone.png' alt='Telephone' /></span><span class='image-text'><a href='tel:" + telephone + "'>" + telephone + "</a></span></li>";
    if (website)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-website.png' alt='Telephone' /></span><span class='image-text'><a href='" + website + "' target='_blank'>" + website + "</a></span></li>";
    if (email)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-email.png' alt='Telephone' /></span><span class='image-text'><a href='mailto:" + email + "'>" + email + "</a></span></li>";
    if (openHours)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-openhours.png' alt='Telephone' /></span><span class='image-text'>" + openHours + "</span></li>";
    if (nearTransport)
        contentTemplate += "<li><span class='image-icon'><img src='images/small-transportation.png' alt='Telephone' /></span><span class='image-text'>" + nearTransport + "</span></li>";
    if (eventStart)
        contentTemplate += "<li><span>" + getCitadel_attr(poi, "#Citadel_eventStart").term + "</span>" + eventStart + "</li>";
    if (eventEnd)
        contentTemplate += "<li><span>" + getCitadel_attr(poi, "#Citadel_eventEnd").term + "</span>" + eventEnd + "</li>";
    if (eventDuration)
        contentTemplate += "<li><span>" + getCitadel_attr(poi, "#Citadel_eventDuration").term + "</span>" + eventDuration + "</li>";
    if (eventPlace)
        contentTemplate += "<li><span>" + getCitadel_attr(poi, "#Citadel_eventPlace").term + "</span>" + eventPlace + "</li>";

    /* Display the rest attributes of the POI */
    for (i = 0; i < otherAttributes.length; i++) {
        contentTemplate += "<li><span>" + otherAttributes[i].term + "</span>" + otherAttributes[i].text + "</li>";
    }

    // Voting system ui
    //contentTemplate += "<li><div class='votePanel'><img id='voteUpButton'  class='voting-icon'  src='images/like-32.png'  onclick='votePoi(\"" + poi.id + "\", 1);' alt='Vote up' /><span id='voteScore'>46</span><img  class='voting-icon'  id='voteDownButton' onclick='votePoi(\"" + poi.id + "\", -1);' src='images/dislike-32.png' alt='Vote down' /></div></li>";
    $('#poiIdForVote').val(currentPoiId);

    contentTemplate += "</ul>" +
            "</div>";
    refreshPoiVotes(currentPoiId);
    return contentTemplate;
}

/* Sets the content of the category 
 * dropdown at the add new marker window 
 */
function setAddNewMarkerCategory()
{
    var categorySelect = "<option value=''></option>";
    //var categorySelect = "";

    $.each(filters, function(k, v) {
        categorySelect += "<option value='" + v.name + "'>" + v.name + "</option>";
    });

    $('#poiCategory').html(categorySelect);
}

/* Sets the content of the Listing Page         */
function setListPagePois()
{
    var contentTemplate = "";

    $.each(pois, function(i, poi) {
        if (isFilterSelected(poi.category)) {

            var category = "";
            if (poi.category) {
                category = "<p>" +
                        poi.category +
                        "</p>";
            }
            var isFavourite = getFavouriteValue(poi.id);
            var imageClass = (isFavourite ? "star" : getMarkerClass(poi.category[0]));
            var className = (isFavourite ? " class='favourite'" : " class='nonfavourite'");

            contentTemplate +=
                    "<li" + className + ">" +
                    "<a href='' onclick='overrideDetailClick(\"" + poi.id + "\"); return false;'>" +
                    "<span class='" + imageClass + " icon'></span>" +
                    "<h3>" + poi.title + "</h3>" +
                    "<h4>" + poi.description + "</h4>" +
                    category +
                    "</a>" +
                    "</li>";
        }
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
 *     #Citadel_telephone                 
 *     #Citadel_website                                      
 *     #Citadel_email                                                           
 *     #Citadel_parkType                                                                                
 *     #Citadel_parkFloors                                                                                                      
 *     #Citadel_parkCapacity                                                                                                                          
 *     #Citadel_image                                                                                                                                               
 *     #Citadel_eventStart                                                                                                                                                                    
 *     #Citadel_eventEnd
 *     #Citadel_eventPlace
 *     #Citadel_eventDuration 
 *     #Citadel_openHours 
 *     #Citadel_nearTransport                                                                                                                                                                                                                                                                                                                                                                                               
 */
function getCitadel_attr(poi, tplIdentifer) {
    var attribute = {
        "term": "",
        "type": "",
        "text": ""
    };
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



/* Bubble on click poi listener */
function overrideDetailClick(id) {
    //Get poi by id
    var poi = pois[id];
    //Pass data to details constructor
    $('#item').html(setDetailPagePoi(poi));

    $.mobile.changePage("#page3", {transition: "none", reverse: false, changeHash: false});
    window.location.href = "#page3?id=" + id;
    showFavouriteButtons(id);

    return true;
}

/* Bubble close click poi listener */
function overrideBubbleCloseClick() {
    infoBubble.close();
    return false;
}


/* Load list page using pois variable */
function loadListPageData() {
    $('#list > ul').html(setListPagePois());
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
        $('#item').html(setDetailPagePoi(pois[pageId]));
        showFavouriteButtons(pageId);
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
        if (lastLoaded != 'nearme') {
            lastLoaded = 'nearme';
            /* Check if we can get geolocation from the browser */
            if (!map)
            {
                initializeMap();

            }
            else
            {
                var mapOptions = {
                    zoom: mapZoom + 10,
                    center: myLatlng
                };
                map.setOptions(mapOptions);
            }

            google.maps.event.trigger(map, 'resize');
            $.mobile.changePage("#page1", {transition: "none"});
            $('.navbar > ul > li > a').removeClass('ui-btn-active');
            $('.pois-nearme').addClass('ui-btn-active');

        }

    });


    /* Click handler for the 'show all' button */
    $('.pois-showall').click(function() {

        $.mobile.changePage("#page1", {transition: "none"});
        $('.navbar > ul > li > a').removeClass('ui-btn-active');
        $('.pois-showall').addClass('ui-btn-active');

        //if (lastLoaded != 'showall') {
        lastLoaded = 'showall';
        var myLocalLatlng = new google.maps.LatLng(mapLat, mapLon);
        //define the map options
        var mapOptions = {
            center: myLocalLatlng,
            zoom: mapZoom,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map.setOptions(mapOptions);
        //  }

    });


    /* Click handler for the 'list' button */
    $('.pois-list').click(function() {

        $.mobile.changePage("#page2", {transition: "none"});
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
            refreshListPageView();      //this was comment!!!??
            pageId = 0;
        }
        if ($(this).attr('id') == 'page3') {
            $('.navbar > ul > li > a').removeClass('ui-btn-active');
        }
    });

    /* Click handler for the filters button */
    $('#filter').click(function() {
        if ($('#map-filter').is(":visible")) {
            $('#map-filter').slideUp();
        } else {
            $('#city-filter').slideUp();
            $('#map-filter').slideDown();
        }
        return false;
    });

    /* Click handler for the city filters button */
    $('#city').click(function() {
        if ($('#city-filter').is(":visible")) {
            $('#city-filter').slideUp();
        } else {
            $('#map-filter').slideUp();
            $('#city-filter').slideDown();
        }
        return false;
    });


    /* Click handler for the aply button inside the filters page. 
     * The markers on the map will be updated according to the 
     * selected filters.
     */
    $('#apply').click(function() {
        if ($('#map-filter').is(":visible")) {
            $('#map-filter').slideUp();
            var checked_objs = $('.map-filter:checked');
            var set_filters = new Array();
            checked_objs.each(function(k, v) {
                set_filters.push($(v).val());
            });
            setSelectedFilters(set_filters);
            addMarkers();
            loadListPageData();
            refreshListPageView();
        } else {
            $('#map-filter').slideDown();
        }
        return false;
    });

   
    /* Adds a poi to favourites list and use
     * local storage to remember my favourites
     */
    $('#addFav').click(function() {
        var id = $(this).attr('rel');
        setLocalValue('favouritepois' + id, true);
        $('#addFav').hide();
        $('#removeFav').show();
        $('#removeFav').attr('rel', id);

        addMarkers();
        loadListPageData();
        refreshListPageView();
    });

    /* Removes a poi from favourites list and 
     * remove it from local storage
     */
    $('#removeFav').click(function() {
        var id = $(this).attr('rel');
        removeLocalValue('favouritepois' + id);
        $('#addFav').show();
        $('#addFav').attr('rel', id);
        $('#removeFav').hide();

        addMarkers();
        loadListPageData();
        refreshListPageView();
    });

    /* Used to filter list page and show
     * only favourites that are currently loaded
     */
    $('#favourites').change(function() {
        var o = $(this);
        if (o.is(':checked')) {
            $('.nonfavourite').addClass('ui-screen-hidden');
        } else {
            $('.ui-input-text').val('');
            $('.nonfavourite').removeClass('ui-screen-hidden');
            $('.favourite').removeClass('ui-screen-hidden');
        }
    });

    /* Used to add a new poi on the map. It creates
     * a draggable marker and user can select a place 
     * on the map for his/her new poi
     */
    $('#addPoi').click(function() {
        if (newMarker == null) {
            var current_latlon = map.getCenter();
            var newMarkerIcon = 'images/big-flag.png';
            newMarker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: current_latlon,
                icon: newMarkerIcon,
                title: 'My new POI'
            });

            var contentTemplate =
                    "<div id='flagBubble'>" +
                    "<a><div class='title'>Drag and drop the flag to the desired location. Then click on it to fill the necessary information.</div></a></div>" +
                    "<div id='bubbleClose'><a href='' onclick='return overrideBubbleCloseClick();'><img src='images/close.png' width='25' height='25' alt='close'/></a></div>";

            infoBubble.setContent(contentTemplate);
            infoBubble.open(map, newMarker);


            // Register Custom "click" Event
            google.maps.event.addListener(newMarker, 'click', function() {
                //close flag info window
                overrideBubbleCloseClick();
                // Get the Current position, where the pointer was dropped
                point = newMarker.getPosition();
                // Center the map at given point
                map.panTo(point);
                setAddNewMarkerCategory();
                geocoder = new google.maps.Geocoder();
                codeLatLng();
                $.mobile.changePage('#newPoiDialog', 'pop', true, true);
            });

        } else {
            alert("A new marker is already placed on the map. Either delete this marker or drag n drop it at the desired location");
        }

        return false;
    });

    /* Uses the Google geocoder to find the address
     * of the new Poi
     */
    function codeLatLng() {
        var lat = parseFloat(point.lat());
        $('#poiLat').val(lat);
        var lng = parseFloat(point.lng());
        $('#poiLng').val(lng);
        console.log(lat, lng);
        var latlng = new google.maps.LatLng(lat, lng);
        geocoder.geocode({
            'latLng': latlng
        }, function(results, status) {

            if (results[0].formatted_address != null) {
                var address = results[0].formatted_address;
                var index = address.indexOf(",");
                var substr = address.substr(0, index);
                console.log("substr = " + substr);
                $('#poiAddress').val(substr);
            }

            var route;
            var streetNum;
            for (var i = 0; i < results[0].address_components.length; i++) {


                if (results[0].address_components[i].types[0] == "postal_code") {
                    $('#poiPostal').val(results[0].address_components[i].long_name);
                }
                else if (results[0].address_components[i].types[0] == "locality") {
                    $('#poiCity').val(results[0].address_components[i].long_name);
                }        
            }
        });

    }

    /*Submit form handler*/
    $("#insertForm").bind("submit", function() {
        var formData = $("#insertForm").serialize();

        // Post the form to the corresponding php script so
        // as to insert the new POI in the database
        $.ajax({
            type: "POST",
            url: insertNewPoiScript,
            cache: false,
            data: formData,
            success: onSuccess,
            error: onError
        });
        return false;
    });



    $("#voteUpButton").bind("click", function() {
        // Checking if user has voted before
        if (typeof(Storage) !== "undefined") {
            if (!localStorage.getItem('votedPoi' + currentPoiId))
            {
                resetVoteLoader();
                $("#poiVote").val('1');
                var formData = $("#insertVote").serialize();
                // Post the form to the corresponding php script so
                // as to insert the new Vote in the database
                $.ajax({
                    type: "POST",
                    url: insertNewVoteScript,
                    cache: false,
                    data: formData,
                    success: onVoteSuccess,
                    error: onVoteFailure
                });
                return false;
            }
            else
            {
                alert('You have already voted!');
            }
        }
        else
        {
            alert("You can not vote because your browser does not support local storage. Please update your browser.");
        }
    });


    /*Submit form handler*/
    $("#voteDownButton").bind("click", function() {
        if (typeof(Storage) !== "undefined") {


            if (!localStorage.getItem('votedPoi' + currentPoiId))
            {
                resetVoteLoader();
                $("#poiVote").val('0');

                var formData = $("#insertVote").serialize();
                // Post the form to the corresponding php script so
                // as to insert the new Vote in the database
                $.ajax({
                    type: "POST",
                    url: insertNewVoteScript,
                    cache: false,
                    data: formData,
                    success: onVoteSuccess,
                    error: onVoteFailure
                });
                return false;
            }
            else
            {
                alert('You have already voted!');
            }
        }
        else
        {
            alert("You can not vote because your browser does not support local storage. Please update your browser.");
        }
    });


    /* Triggered when user deletes previously 
     * added new marker 
     */
    $('#addNewPoiDelete').click(function() {
        deleteMarkerNCloseDialog();
    });


}); // end $(document).ready

function refreshPoiVotes(poiId)
{
    $.ajax({
        type: "GET",
        url: getPoiVotesScript + "?poiId=" + poiId,
        cache: false,
        success: onRefreshPoiVotesSuccess,
        error: onRefreshPoiVotesFailure
    });

}
/*Called after a successful poi insertion*/
function onSuccess(data, status)
{
    $('#insertForm')[0].reset();
    deleteMarkerNCloseDialog();
    globalInit();
}

function onRefreshBubblePoiVotesSuccess(data, status)
{
    var array = JSON.parse(data);
    $('#bubbleDownVotes').html(array[1]);
    $('#bubbleUpVotes').html(array[0]);
}
// Called when failing to retrieve the votes of a poi
function onRefreshBubblePoiVotesFailure(data, status)
{
    $('#downVoteScore').html('error');
    $('#upVoteScore').html('error');
}


function onRefreshPoiVotesSuccess(data, status)
{
    var array = JSON.parse(data);
    $('#downVoteScore').html(array[1]);
    $('#upVoteScore').html(array[0]);
}
// Called when failing to retrieve the votes of a poi
function onRefreshPoiVotesFailure(data, status)
{
    $('#downVoteScore').html('error');
    $('#upVoteScore').html('error');
}

/*Called after a successful poi insertion*/
function onVoteSuccess(data, status)
{
    console.log("Vote success");
    alert('Vote submitted!');
    $('#insertVote')[0].reset();
    localStorage.setItem('votedPoi' + currentPoiId, "1");
    refreshPoiVotes(currentPoiId);
}
/*Called after a unsuccessful poi insertion*/
function onError(data, status)
{
    // handle an error
}

function onVoteFailure(data, status)
{
    console.log('vote failed', data, status);
    alert('There was a problem submitting your vote, please try again later.');
}

/* Delete the new marker and close dialog window */
function deleteMarkerNCloseDialog() {
    if (newMarker != null) {
        newMarker.setMap(null);
        newMarker = null;
    }

    $('#newPoiDialog').dialog('close');
}


/* Sets the available filters  */
function setFilters() {
    var filters_html = "";

    for (i = 0; i < filters.length; i++) {
        var filter = filters[i];
        var checked = filter.selected ? ' checked' : '';

        var filterName = filter.name;
        var filterType = filter.type;

        console.log("filter type = " + filterType);
        if (filterName == "Parking") {

            filters_html += "<input type='checkbox'" + checked + " name='map-filter' id='map-filter" + i + "' class='map-filter' value=\"" + filterName + "\" />" +
                    "<label for='map-filter" + i + "'><img id='img_style' src='images/blue.png'/> " + filterName + " , (" + filterType + ")</label>";

        }
        else {
            //console.log("filter name = " + filterName);
            filters_html += "<input type='checkbox'" + checked + " name='map-filter' id='map-filter" + i + "' class='map-filter' value=\"" + filterName + "\" />" +
                    "<label for='map-filter" + i + "'><img id='img_style' src='images/pin" + i % 16 + ".png'/> " + filterName + " , (" + filterType + ")</label>";
        }
    }

    $('#map-filter > div > fieldset').html(filters_html);
    $('#map-filter > div > fieldset > input').checkboxradio({mini: true});
}

/* Sets the available city filters  */
function setCityFilters() {
    var filters_html = "";

    for (i = 0; i < cities.length; i++) {
        var city = cities[i];

        var filterName = city.name;
        var filterLat = city.lat;
        var filterLon = city.lon;
        var coords = filterLat + "/" + filterLon;

        console.log("city type = " + filterName);

        //console.log("filter name = " + filterName);
        filters_html += "<input type='radio' " + " name='city' id='city-filter" + i + "' class='city-filter' value=\"" + coords + "\" />" +
                "<label for='city-filter" + i + "'>\n\
 " + filterName + " </label>";
    }

    $('#city-filter > div > fieldset').html(filters_html);
    $('#city-filter > div > fieldset > input').checkboxradio({mini: true});

    $('input[type=radio][name=city]').click(function() {
        console.log("pressed");
        if ($('#city-filter').is(":visible")) {
            $('#city-filter').slideUp();
            var val = $('input[name=city]:checked').val();
            console.log("val = ", val);
            var coordsCity = val.split("/");
            console.log(coordsCity);
            mapLat = coordsCity[0];
            mapLon = coordsCity[1];

            var center = new google.maps.LatLng(mapLat, mapLon);
            // using global variable:
            map.panTo(center);
            map.setZoom(10);

        } else {
            $('#city-filter').slideDown();
        }
        return false;
    });
}


/* Refreshes the map when the mobile device
 *  orientation changes
 */
$(window).resize(function() {
    hideAddressBar();
    setTimeout(function() {
        fixMapHeight();
        refreshMap();
    }, 500);
});

/* Used to fix map height depending on device screen size */
function fixMapHeight() {
    var sh = window.innerHeight ? window.innerHeight : $(window).height();
    var bh = $('.page > header:first').height();
    var diff = sh - bh;

    $('#map-container').height(diff);
    $('#page1').height(sh);
}

/* Used to hide the nav bar */
function hideAddressBar() {
    //Fake mobile by increasing page height, so address bar can hide.
    var sh = window.innerHeight ? window.innerHeight : $(window).height();
    $('#page1').height(sh * 2);
    $('#map-container').height(sh * 2);

    var doc = $(document);
    var win = this;

    // If there's a hash, or addEventListener is undefined, stop here
    if (!location.hash && win.addEventListener) {
        window.scrollTo(0, 1);
    }
}

/* 
 * Matches the selected filters
 * with POIS categories
 */
function isFilterSelected(categories) {
    var found = false;
    $.each(categories, function(k, v) {
        var filter = $.grep(filters, function(e) {
            return (e.name == v && e.selected)
        });

        if (filter.length == 1) {
            found = true;
            return false;
        }
    });

    if (found)
        return true;

    return false;
}

/* Checks whether or not local storage is supported */
function supportsLocalStorage() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}

/* Gets local variable's value */
function getLocalValue(key) {
    return localStorage.getItem(key);

}

/* Sets local variable's value */
function setLocalValue(key, value) {
    localStorage.setItem(key, value);
}

/* Empty local variable */
function removeLocalValue(key) {
    localStorage.removeItem(key);
}
/* Retrieve favourite value */
function getFavouriteValue(id) {
    return getLocalValue('favouritepois' + id) ? true : false;
}
/* If local storage is supported show favourite button */
function showFavouriteButtons(id) {
    if (supportsLocalStorage()) {
        var favourite = getFavouriteValue(id);
        if (favourite) {
            $('#addFav').hide();
            $('#removeFav').show();
            $('#removeFav').attr('rel', id);
        } else {
            $('#addFav').show();
            $('#addFav').attr('rel', id);
            $('#removeFav').hide();
        }
        $('#page3 > footer').show();
    }
}
/* Older versions of android do not allow scroll for divs. 
 * Use the functions bellow to fix this bug. 
 */
function isTouchDevice() {
    try {
        document.createEvent("TouchEvent");
        return true;
    } catch (e) {
        return false;
    }
}
function touchScroll(id) {
    var versionRX = new RegExp(/Android [0-9]/);
    var versionS = new String(versionRX.exec(navigator.userAgent));
    var version = parseInt(versionS.replace("Android ", ""));

    if (isTouchDevice() && version < 4) { //if touch events exist and older than android 4
        var el = document.getElementById(id);
        var scrollStartPos = 0;

        document.getElementById(id).addEventListener("touchstart", function(event) {
            scrollStartPos = this.scrollTop + event.touches[0].pageY;
            event.preventDefault();
        }, false);

        document.getElementById(id).addEventListener("touchmove", function(event) {
            this.scrollTop = scrollStartPos - event.touches[0].pageY;
            event.preventDefault();
        }, false);
    }
}

/*
 * Used to update filters object list with newly selected filters
 */
function setSelectedFilters(selected) {
    $.each(filters, function(k, v) {
        if ($.inArray(v.name, selected) > -1) {
            filters[k].selected = true;
        } else {
            filters[k].selected = false;
        }
    });
}