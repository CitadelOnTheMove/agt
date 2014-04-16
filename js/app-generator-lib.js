/*****************Global variables********************/
var newMarker = null;
var point = null;
var geocoder;
var currentPoi;
var currentPoiId;
var myLatlng;
// timeout for geolocation failure
var location_timeout;
var poisArrayInitialised = false;
var selectedCityId;
var retrievedCitiesDatasets = new Array();
var startTime;

/****************** Functions *****************************/

/* Initialization function.
 * It is called once when the page is load
 */
function globalInit() {
    $.mobile.showPageLoadingMsg();
    $('#progressbar').hide();
    jQMProgressBar('progressbar')
            .setOuterTheme('s')
            .setInnerTheme('b')
            .isMini(true)
            .setMax(100)
            .setStartFrom(0)
            .build();

    setCityFilters();
    initializeMap();
    hideAddressBar();
    setTimeout(function() {
        fixMapHeight();
    }, 500);

    //Enable scroll for older versions of android
    touchScroll('mapFilterList');
    touchScroll('cityFilterList');
}

/* Returns an array of poi objects.
 * The poi object contains the data described in the 
 * Citadel Common POI format
 */
function getPoisFromDataset(data)
{
    //console.log("getPoisFromDataset() called");
    startTime = new Date();

    if (data.status === "success")
    {
        if (!poisArrayInitialised) {
            var k = 0;
            filters = data.filters;
            $('.ui-title').html(data.appName);

            $.each(data.applicationData, function(i, datasetObject) {
                var positionInDataset = 0;

                var length = datasetObject.dataset.poi.length;
                var index = 0;

                var processPoisBatch = function() {
                    for (; index < length; index++) {
                        var poi = datasetObject.dataset.poi[index];
                        poi.positionInDataset = positionInDataset;
                        positionInDataset++;
                        poi.id = k;
                        pois[k] = poi;
                        k++;

                        if (index + 1 < length && index % 100 === 0 && index > 0) {
                            updateProgressBar('Loading data...', ((index / length) * 5) + 10);

                            // Last iteration
                            if (index === length - 1)
                            {
                                getPoisFromDatasetCallback();
                            }
                            else {
                                setTimeout(processPoisBatch, 5);
                                index++;
                                break;
                            }
                        }
                        // Last iteration
                        if (index === length - 1)
                        {
                            getPoisFromDatasetCallback();
                        }
                    }
                };
                processPoisBatch();
            });

            poisArrayInitialised = true;
        }
        /* 
         * Add pois to the initial array of poi objects.
         */
        else {
            $.each(data.filters, function(i, filterObject) {
                filters.push(filterObject);
            });

            var k = pois.length;
            $.each(data.applicationData, function(i, datasetObject) {
                var positionInDataset = 0;

                var length = datasetObject.dataset.poi.length;
                var index = 0;

                var processPoisBatch = function() {
                    for (; index < length; index++) {
                        var poi = datasetObject.dataset.poi[index];
                        poi.positionInDataset = positionInDataset;
                        positionInDataset++;
                        poi.id = k;
                        pois.push(poi);
                        k++;

                        if (index + 1 < length && index % 100 === 0 && index > 0) {
                            updateProgressBar('Loading data...', ((index / length) * 5) + 10);

                            if (index === length - 1)
                            {
                                getPoisFromDatasetCallback();
                            }
                            else {
                                setTimeout(processPoisBatch, 5);
                                index++;
                                break;
                            }
                        }
                        // Last iteration
                        if (index === length - 1)
                        {
                            getPoisFromDatasetCallback();
                        }
                    }
                };
                processPoisBatch();
            });
        }

    }
    else if (data.status === "failed") {
        alert(data.message);
        console.log(data.error);
    }
    else {
        alert(data.message);
        console.log("Error loading data:" + data.error);
    }

}

function getPoisFromDatasetCallback() 
{
    var endTime = new Date();
    var getPoisFromDatasetDuration = endTime - startTime;
    //console.log("getPoisFromDataset took: ", getPoisFromDatasetDuration);
    setFiltersByCityId(selectedCityId);
}


/* Initialises a google map using map api v3 */
function initializeMap() 
{
    //console.log("initializeMap() called");
    var startTime = new Date();

    var mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    /* instantiate the map wih the options and put it in the div holder, "map-canvas" */
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    /* If the app has only one city, set it directly as the active one */
    if (cities.length === 1) {
        $.mobile.hidePageLoadingMsg();
        selectedCityId = cities[0].id;
        retrievedCitiesDatasets.push(selectedCityId);

        $('input[id=city-filter' + cities[0].id + ']').attr('checked', 'checked').checkboxradio("refresh");
        centerToCity($('input[id=city-filter' + cities[0].id + ']').val());

        $.ajax({
            type: "GET",
            url: "dataset.php?uid=" + appId + "&cityId=" + selectedCityId,
            cache: false,
            success: onDatasetSuccess,
            error: onDatasetFailure
        });
    }
    else if (navigator.geolocation) {
        // Set timeout to 15 secs
        location_timeout = setTimeout("geolocFail()", 15000);
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        showDefaultMap();
    }

    var endTime = new Date();
    var duration = endTime - startTime;
    //console.log("initializeMap took: ", duration);
}

function geolocFail() {
    showDefaultMap();
}

function showDefaultMap() 
{
    $.mobile.hidePageLoadingMsg();
    myLatlng = new google.maps.LatLng(mapLat, mapLon);
    mapOptions = {
        center: myLatlng,
        zoom: mapZoom
    };
    map.setOptions(mapOptions);
    map.setCenter(myLatlng);
    alert("Geolocation not available, please select a city from the upper right corner");
}

function showPosition(position) 
{
    clearTimeout(location_timeout);
    $.mobile.hidePageLoadingMsg();
    myLatlng = new google.maps.LatLng(parseFloat(position.coords.latitude), parseFloat(position.coords.longitude));
    var datasetFound = false;
    if (cities.length == 0) {
        alert("Missing application id. No data is loaded.");
    }

    /* Check if any of the app cities is close to the user's current location
     * and if found make it active */
    else if (cities.length > 1) {
        
        var p1 = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

        for (i = 0; i < cities.length; i++) {
            var city = cities[i];
            var cityId = city.id;
            var p2 = new google.maps.LatLng(city.lat, city.lon);
            var distance = calcDistance(p1, p2);

            if (distance < maxCityDistance) {
                selectedCityId = cityId;
                retrievedCitiesDatasets.push(selectedCityId);

                $('input[id=city-filter' + selectedCityId + ']').attr('checked', 'checked').checkboxradio("refresh");
                centerToCity($('input[id=city-filter' + selectedCityId + ']').val());

                $.ajax({
                    type: "GET",
                    url: "dataset.php?uid=" + appId + "&cityId=" + selectedCityId,
                    cache: false,
                    success: onDatasetSuccess,
                    error: onDatasetFailure
                });
                datasetFound = true;
                break;
            }
        }
    }
    else if (!datasetFound) {
        alert("There is no dataset available near your current position, please select a city on the right corner");
    }
    var currentMarker = new google.maps.Marker({
        position: myLatlng,
        map: map
    });
    //define the map options
    mapOptions = {
        center: myLatlng,
        zoom: mapZoom + 10
    };
    map.setOptions(mapOptions);
    map.setCenter(myLatlng);
}

function showError(error) {
    clearTimeout(location_timeout);
    console.warn('ERROR(' + error.code + '): ' + error.message);

    $.mobile.hidePageLoadingMsg();
    switch (error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
            break;
    }
    showDefaultMap();
}


/**calculates distance between two points in km's */
function calcDistance(p1, p2) {
    return (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000).toFixed(2);
}


/**markers of one category will be added on the map 
 * based on the selected city*/
function setFiltersByCityId(cityId)
{
    //console.log("setFiltersByCityId() called");
    startTime = new Date();

    var currentCityId = cityId;
    var selected = false;

    var length = filters.length;
    var index = 0;

    var processFilters = function() {

        for (; index < length; index++) {
            var filter = filters[index];
            filter.selected = false;

            if (filter.cityId === currentCityId) {
                filter.isVisible = true;
                if (!selected) {
                    filter.selected = true;
                    selected = true;
                }
            }
            else
                filter.isVisible = false;

            if (index + 1 < length && index % 20 === 0) {
                updateProgressBar('Loading data...', ((index / length) * 5) + 15);

                if (index === length - 1)
                {
                    setFiltersByCityIdCallback();
                }
                else {
                    setTimeout(processFilters, 5);
                    index++;
                    break;
                }
            }

            if (index === length - 1)
            {
                setFiltersByCityIdCallback();
            }
        }
    };
    processFilters();
}


function setFiltersByCityIdCallback() 
{
    var endTime = new Date();
    var duration = endTime - startTime;
    //console.log("setFiltersByCityId took: ", duration);
    setFilters();
}

/* Adds all the markers on the global map object */
function addMarkers()
{
    //console.log("addMarkers() called");
    startTime = new Date();

    for (var i = 0; i < markersArray.length; i++) {
        if (typeof markersArray[i] == "object") {
            markersArray[i].setMap(null);
        }
    }
    markersArray = new Array();

    if (infoBubble)
        overrideBubbleCloseClick();

    /* var oms will hold all the markers so as to resolve
     * the issue of overlapping ones
     */
    var oms = new OverlappingMarkerSpiderfier(map);

    /* We initialize the infobubble styling */
    infoBubble = new InfoBubble({
        shadowStyle: 1,
        padding: 5,
        backgroundColor: bubbleColor,
        borderRadius: 10,
        arrowSize: 10,
        borderWidth: 2,
        maxWidth: 300,
        borderColor: '#3A3A3A',
        disableAutoPan: false,
        hideCloseButton: true
    });

    /* For every POI we add a marker with an attached infoBubble */
    var length = pois.length;
    var index = 0;
    
    var processMarkers = function() {
        for (; index < length; index++) {
            var poi = pois[index];

            if (isFilterSelected(poi.category, poi.cityId)) {
                /*  posList contains a list of space separated coordinates.
                 *  The first two are lat and lon
                 */
                var coords = poi.location.point.pos.posList.split(" ");
                var current_markerpos = new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1]));
                var marker_image = getFavouriteValue(poi.id) ? "images/star.png" : getMarkerImage(poi.category[0], poi.cityId);
                var current_marker = new google.maps.Marker({
                    position: current_markerpos,
                    map: map,
                    icon: marker_image
                });
                current_marker.citadelPoi = poi;

                markersArray[poi.id] = current_marker;
                oms.addMarker(current_marker);
                google.maps.event.addListener(current_marker, 'click', function() {
                    infoBubble.setContent(setInfoWindowPoi(this.citadelPoi));
                    infoBubble.open(map, this);
                    setTimeout("$('#poiBubble').parent().parent().css('overflow', 'hidden')", 100);
                });
            }

            if (index + 1 < length && index % 100 === 0) {
                updateProgressBar('Adding markers...', ((index / length) * 45) + 50);

                // Last iteration
                if (index === length - 1)
                {
                    addMarkersCallback();
                }
                else {
                    setTimeout(processMarkers, 5);
                    index++;
                    break;
                }
            }

            if (index === length - 1)
            {
                addMarkersCallback();
            }
        }
    };
    processMarkers();
}


function addMarkersCallback() 
{
    var endTime = new Date();
    var duration = endTime - startTime;
   //console.log("addMarkers took: ", duration);

    loadDetailsPage();
    loadListPageData();
    refreshListPageView();
    //$.mobile.hidePageLoadingMsg();
    updateProgressBar('Done', 100);
    $('#progressbar').hide("slow", function() {
    });
}

/*
 * Returns the marker image picking a unique color for every category
 */

function getMarkerImage(category, cityId) 
{
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
            if (filters[i].name.toLowerCase() == category.toLowerCase() && filters[i].cityId == cityId) {
                return coloredMarkers[i % 16];
            }
        }
    }
}


function getMarkerClass(category, cityId) 
{
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
            if (filters[i].name.toLowerCase() == category.toLowerCase() && filters[i].cityId == cityId) {
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
            "</div>";
    if (poi.location.address.value) {
        contentTemplate += "<div class='address'>" + poi.location.address.value + "</div>";
    }


    contentTemplate += "\n" + category +
            "<span class='bubbleUpVoteWrapper'><img src='images/like-32.png'/><span id='bubbleUpVotes'></span></span><span  class='bubbleDownVoteWrapper'><img src='images/dislike-32.png'/><span id='bubbleDownVotes'></span></span>" +
            "</a></div><div id='bubbleClose'><a href='' onclick='return overrideBubbleCloseClick();'><img src='images/close.png' width='25' height='25' alt='close' /></a></div>";
    return contentTemplate;
}

function resetVoteLoader()
{
    $('#downVoteScore').html("<img  src='images/loader.png'  />");
    $('#upVoteScore').html("<img  src='images/loader.png'  />");
}


/* Sets the content of the details page for the given 
 * poi
 */
function setDetailPagePoi(poi)
{
    resetVoteLoader();
    currentPoi = poi;
    currentPoiId = poi.id;
    var latlon = poi.location.point.pos.posList;
    /* Get the Event specific attributes of the POI */
    var image = getCitadel_attr(poi, "#Citadel_image").text;
    var contentTemplate =
            "<div class='poi-data'>" +
            "<ul>";
    /* If an image exists,print it first. */
    if (image)
        contentTemplate += "<li class='image'><img src='" + image + "' alt='Event image' /></li>";
    /* Print standard poi details */
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

    contentTemplate += "<li><a href='#page1' onclick='seeOnMap(); return false;'><img class='seeOnMap' src='images/seeOnMap.png' alt='see POI on map'/></a></li>";
    /* Print further poi details found in the attribute array */
    $.each(poi.attribute, function(i, attr) {
        if (attr.text != "")
            contentTemplate += "<li><span>" + attr.term + "</span>" + attr.text + "</li>";
    });
    // Voting system ui
    $('#poiIdForVote').val(currentPoiId);
    contentTemplate += "</ul>" +
            "</div>";
    refreshPoiVotes(currentPoiId);
    return contentTemplate;
}


/* Sets the content of the Listing Page         */
function setListPagePois()
{
    var contentTemplate = "";
    $.each(pois, function(i, poi) {

        if (isFilterSelected(poi.category, poi.cityId)) {
            var category = "";
            if (poi.category && poi.cityId) {
                category = "<p>" +
                        poi.category +
                        "</p>";
            }
            var isFavourite = getFavouriteValue(poi.id);
            var imageClass = (isFavourite ? "star" : getMarkerClass(poi.category[0], poi.cityId));
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


/*  Returns an array of all the attributes of 
 *  the given poi.    
 */
function get_all_attrs(poi) {
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

/* Bubble on click poi listener */
function overrideDetailClick(id) 
{
    //Get poi by id
    var poi = pois[id];
    //Pass data to details constructor
    $('#item').html(setDetailPagePoi(poi));
    $.mobile.changePage("#page3", {transition: "none", reverse: false, changeHash: false});
    window.location.href = "#page3"; /*?id=" + id*/
    showFavouriteButtons(id);
    return true;
}

/* Bubble close click poi listener */
function overrideBubbleCloseClick() {
    infoBubble.close();
    return false;
}


/* Load list page using pois variable */
function loadListPageData() 
{
    //console.log("loadListPageData() called");
    var startTime = new Date();

    $('#list > ul').html(setListPagePois());

    var endTime = new Date();
    var duration = endTime - startTime;
    //console.log("loadListPageData took: ", duration);
}

/* Refreshes the list of POIS in the List Page */
function refreshListPageView() 
{
    //console.log("refreshListPageView() called");
    var startTime = new Date();

    if ($("#list > ul").hasClass("ui-listview")) {
        $("#list > ul").listview('refresh');
    }

    var endTime = new Date();
    var duration = endTime - startTime;
    //console.log("refreshListPageView took: ", duration);
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
    //console.log("loadDetailsPage() called");
    var startTime = new Date();

    if (pageId != 0) {
        $('#item').html(setDetailPagePoi(pois[pageId]));
        showFavouriteButtons(pageId);
        pageId = 0;
    }
    var endTime = new Date();
    var loadDetailsPageDuration = endTime - startTime;
    //console.log("loadDetailsPage took: ", loadDetailsPageDuration);
}


function seeOnMap() 
{
    map.setZoom(16);
    infoBubble.setContent(setInfoWindowPoi(currentPoi));
    infoBubble.open(map, markersArray[currentPoiId]);
    refreshPoiVotes(currentPoi);
}


/****************** Event Handlers*************************/

$(document).ready(function() {

    /* Click handler for the 'near me' button */
    $('.pois-nearme').click(function() {

        $('.navbar > ul > li > a').removeClass('ui-btn-active');
        $('.pois-nearme').addClass('ui-btn-active');
        $.mobile.changePage("#page1", {transition: "none"});
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
            refreshListPageView(); //this was comment!!!??
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


    /* Click handler for the city radio button inside the city filters.
     * The map will be recentered to the center of the selected city
     *  and markers of one category will be added on the map.
     */
    $('#cityFilterList').on("change", "input[type=radio][name=city]", function() 
    {
        selectedCityId = $(this).attr('id').substring(11);

        if ($('#city-filter').is(":visible")) {
            $('#city-filter').slideUp();

            var val = $('input[name=city]:checked').val();
            centerToCity(val);

        } else {
            $('#city-filter').slideDown();
        }

        if (cities.length > 1) {
            if ($.inArray(selectedCityId, retrievedCitiesDatasets) > -1) {
                 setFiltersByCityId(selectedCityId);
            }
            else {
                retrievedCitiesDatasets.push(selectedCityId);
            
                $.ajax({
                    type: "GET",
                    dataType: 'json',
                    url: "dataset.php?uid=" + appId + "&cityId=" + selectedCityId, 
                    cache: false,
                    progress: function(e) {
                        updateProgressBar('Loading data...', 10);
                    },
                    success: onDatasetSuccess,
                    error: onDatasetFailure
                });
            }
        }
        return false;
    });


    /* Click handler for the aply button inside the filters page. 
     * The markers on the map will be updated according to the 
     * selected filters.
     */
    $('#apply').click(function() {
        if ($('#map-filter').is(":visible")) {
            //$.mobile.showPageLoadingMsg();
            $('#map-filter').slideUp('400', function() {
                var checked_objs = $('.map-filter:checked');
                var set_filters = new Array();

                checked_objs.each(function(k, v) {
                    set_filters.push($(v).val());
                });
                setSelectedFilters(set_filters);
                addMarkers();
            });

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


    $("#voteUpButton").bind("click", function() {
        // Checking if user has voted before
        if (typeof(Storage) !== "undefined") {
            if (!localStorage.getItem('votedPoi' + currentPoiId))
            {
                resetVoteLoader();
                $("#poiVote").val('1');
                var formData = $("#insertVote").serialize();
                /* Post the form to the corresponding php script so
                 /* as to insert the new Vote in the database */
                $.ajax({
                    type: "POST", url: insertNewVoteScript,
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
                /* Post the form to the corresponding php script so
                 /* as to insert the new Vote in the database */
                $.ajax({
                    type: "POST", url: insertNewVoteScript,
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
}); // end $(document).ready


function updateProgressBar(labelText, value) {
    //console.log(labelText, value)
    $('#progressbar').show("slow", function() {
    });
     jQMProgressBar('progressbar').setValue(value);
    $('.ui-jqm-progressbar-label').html(labelText);
}


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

function onRefreshBubblePoiVotesSuccess(data, status)
{
    var array = JSON.parse(data);
    $('#bubbleDownVotes').html(array[1]);
    $('#bubbleUpVotes').html(array[0]);
}

/* Called when failing to retrieve the votes of a poi */
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

/* Called when failing to retrieve the votes of a poi */
function onRefreshPoiVotesFailure(data, status)
{
    $('#downVoteScore').html('error');
    $('#upVoteScore').html('error');
}

/*Called after a successful poi insertion */
function onVoteSuccess(data, status)
{
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
    alert('There was a problem submitting your vote, please try again later.');
}

/*Called after a successful dataset fetch*/
function onDatasetSuccess(data, status)
{
    //console.log("onDatasetSuccess() called");
    var startTime = new Date();

    getPoisFromDataset(data);

    var endTime = new Date();
    var onDatasetSuccessduration = endTime - startTime;
    //console.log("onDatasetSuccess took: ", onDatasetSuccessduration);
}

function onDatasetFailure(data, status)
{
    alert('There was a problem fetching the dataset.');
}

/* Sets the available filters  */
function setFilters() 
{
    //console.log("setFiltersCalled, " + filters.length + " filters");
    startTime = new Date();
   
    /*enable filter selection*/
    $('#filter').removeClass('ui-disabled');

    var length = filters.length;
    var index = 0;
    filters_html = "";

    /*Define processSetFilters function to allow the filters to be processed in batches */
    var processSetFilters = function() {
        for (; index < length; index++) {
            var filter = filters[index];

            if (filter.isVisible) {
                var checked = filter.selected ? ' checked' : '';
                var filterName = filter.name;
                var filterType = filter.type;
                var filterCityId = filter.cityId;
                var filterPoisCounter = filter.poisCounter;
                var filterValue = filterName + "/" + filterCityId;
                filters_html += "<input type='checkbox'" + checked + " name='map-filter' id='map-filter" + index + "' class='map-filter' value=\"" + filterValue + "\" />" +
                        "<label for='map-filter" + index + "'><img id='img_style' src='images/pin" + index % 16 + ".png'/> " + filterName + " , (" + filterPoisCounter + " " + filterType + ")</label>";
            }

            /* Every 20 iterations, we set a 5 second timeout, to prevent the browser from freezing*/
            if (index + 1 < length && index % 20 === 0) {

                updateProgressBar('Setting Categories...', ((index / length) * 30) + 20);

                /* Covers the case where this is also the last iteration */
                if (index === length - 1)
                {
                    setFiltersCallback();
                }
                else {
                    setTimeout(processSetFilters, 5);
                    index++;
                    break;
                }
            }

            // Last iteration
            if (index === length - 1)
            {
                setFiltersCallback();
            }
        }
    };
    /*Starting batch processing */
    processSetFilters();
}


function setFiltersCallback() 
{
    $('#map-filter > div > fieldset').html(filters_html);
    $('#map-filter > div > fieldset > input').checkboxradio({mini: true});

    var endTime = new Date();
    var setFiltersDuration = endTime - startTime;
    //console.log("setFilters took: ", setFiltersDuration);

    addMarkers();
}

/* Sets the available city filters  */
function setCityFilters() 
{
    //console.log("setCityFilters() called");
    var startTime = new Date();

    var filters_html = "";
    for (i = 0; i < cities.length; i++) {
        var city = cities[i];
        var filterName = city.name;
        var filterLat = city.lat;
        var filterLon = city.lon;
        var coords = filterLat + "/" + filterLon;
        filters_html += "<input type='radio' " + " name='city' id='city-filter" + city.id + "' class='city-filter' value=\"" + coords + "\" />" +
                "<label for='city-filter" + city.id + "'>\n\
    " + filterName + " </label>";
    }

    $('#city-filter > div > fieldset').html(filters_html);
    $('#city-filter > div > fieldset > input').checkboxradio({mini: true});

    var endTime = new Date();
    var duration = endTime - startTime;
    //console.log("setCityFilters took: ", duration);
}

/*  Centers the map to the given coordinates
 *  coordinatesString: string of the form "lat/long"
 */
function centerToCity(coordinatesString) 
{
    var coordsCity = coordinatesString.split("/");
    mapLat = coordsCity[0];
    mapLon = coordsCity[1];
    map.panTo(new google.maps.LatLng(mapLat, mapLon));
    map.setZoom(14);
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
function isFilterSelected(categories, cityId) {
    var found = false;
    var filter;
    $.each(categories, function(index, categoryName) {
        var id = cityId;
        filter = $.grep(filters, function(e) {
            return (e.name.toLowerCase() == categoryName.toLowerCase() && e.selected && e.cityId == id);
        });
        if (filter.length == 1) {
            found = true;
            return false;
        }
    });
    if (found) {
        return true;
    }
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

/* Sets local variable's value *
 $('#removeFav').hide();
 }
 $('#page3 > footer').show();
 }
 }
 
 /* Older versions of android do not allow scroll for divs. 
 * Use the functions bellow to fix this bug. 
 */ function isTouchDevice() {
    try {
        document.createEvent("TouchEvent");
        return true;
    } catch (e) {
        return false;
    }
}

function touchScroll(id) 
{
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
function setSelectedFilters(selectedFilterValues) 
{
    //console.log("setSelectedFilters() called");
    var startTime = new Date();

    $.each(filters, function(index, filter) {

        if ($.inArray(filter.name + "/" + filter.cityId, selectedFilterValues) > -1) {
            filters[index].selected = true;
        } else {
            filters[index].selected = false;
        }
    });

    var endTime = new Date();
    var duration = endTime - startTime;
    //console.log("setSelectedFilters took: ", duration);
}