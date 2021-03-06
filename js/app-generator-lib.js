/*****************Global variables********************/
var newMarker = null;
var point = null;
var geocoder;

var currentPoiId;
var myLatlng;
// timeout for geolocation failure
var location_timeout;
var poisArrayInitialised = false;
var selectedCityId;
var retrievedCitiesDatasets = new Array();
var startTime;
var filtersIndex = 0;
var jsonData = new Array();
var markerArray = [];
var directionsDisplay;
var directionsService;
var stepDisplay;
var datasetSupportsDates = true;
var dateFilterActive = false;
var currentPoi;

var currentMarker;
var callbackAppId;
var callbackCityId;
var callbackDatasets;
var callbackShortestIndex;
var categories = [];

function Transit(lat, lon, type) {
    this.lat = lat;
    this.lon = lon;
    this.type = type;
}

var currentTransit = new Transit("", "", "");

/****************** Functions *****************************/

/* Initialization function.
 * It is called once when the page is load
 */
function globalInit() {
    $.mobile.loading("show");
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
    
    if(datasetPreview){
        activateDatasetPreviewMode();
    }
    

    //Enable scroll for older versions of android
    touchScroll('mapFilterList');
    touchScroll('cityFilterList');
}

function activateDatasetPreviewMode()
{
    $('#city').addClass('ui-disabled');
    $('#filter').addClass('ui-disabled');
    $('#addFav').addClass('ui-disabled');
    $('.votePanel').hide();
    $(".favourites-button").hide();
}

/* Returns an array of poi objects.
 * The poi object contains the data described in the 
 * Citadel Common POI format
 */
function getPoisFromDataset(data)
{

    if (data.status === "success" || data.status == "SUCCESS")
    {
        if (!poisArrayInitialised || datasetPreview) {

            var k = 0;
            filters = data.filters;
            //$('.ui-title').html(data.appName);

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

/* Returns an array of poi objects.
 * The poi object contains the data described in the 
 * Citadel Common POI format
 */
function getPoisFromDatasetDiscovery(data)
{
    console.log(data);
    console.log("getPoisFromDatasetDiscovery() called");
    data.status = "success";
    if (data.status === "success" || data.status == "SUCCESS")
    {
        $.each(data.filters, function(i, filterObject) {
            console.log("pushing filter");
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
                            getPoisFromDatasetDiscoveryCallback();
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
                        getPoisFromDatasetDiscoveryCallback();
                    }
                }
            };
            processPoisBatch();
        });
        
        var index = (cities.length)-1;
        console.log(index);
        console.log(cities);
        console.log(categories);
        $('input[id=city-filter' + cities[index].id + ']').attr('checked', 'checked').checkboxradio("refresh");
        setCityFilters();   
        setFiltersByCityId(selectedCityId);
        centerToCity($('input[id=city-filter' + cities[index].id + ']').val());
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

function getPoisFromDatasetDiscoveryCallback()
{
    addMarkers();
}

function getPoisFromDatasetCallback()
{

    if (datasetPreview)
    {
        addMarkers();
    }
    else
    {
        setFiltersByCityId(selectedCityId);
    }
}


/* Initialises a google map using map api v3 */
function initializeMap()
{
    /* If the app has only one city, set it directly as the active one */
    directionsService = new google.maps.DirectionsService();

    $('.ui-title').html(appName);
    var mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    /* instantiate the map wih the options and put it in the div holder, "map-canvas" */
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

    // Create a renderer for directions and bind it to the map.
    var rendererOptions = {
        map: map
    }
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

    // Instantiate an info window to hold step text.
    stepDisplay = new google.maps.InfoWindow();


    if (cities.length === 1) {
        //  $.mobile.hidePageLoadingMsg();
        $.mobile.loading("hide");
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
    else if (datasetPreview)
    {
        //  $.mobile.hidePageLoadingMsg();
        $.mobile.loading("hide");
        $.ajax({
            type: "GET",
            url: "dataset.php?preview=true&converterdatasetID=" + datasetPreviewId,
            cache: false,
            success: onDatasetSuccess,
            error: onDatasetFailure
        });
    }
    else if (navigator.geolocation) {
        // Set timeout to 15 secs
        location_timeout = setTimeout("geolocFail()", 15000);
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    }
    
    else {
        showDefaultMap();
    }
}

function geolocFail() {
    showDefaultMap();
}

function showDefaultMap()
{
    // $.mobile.hidePageLoadingMsg();
    $.mobile.loading("hide");
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
    console.log("geolocation is supported");
    clearTimeout(location_timeout);
    // $.mobile.hidePageLoadingMsg();
    $.mobile.loading("hide");
    myLatlng = new google.maps.LatLng(parseFloat(position.coords.latitude), parseFloat(position.coords.longitude));
    var datasetFound = false;
    if (cities.length == 0) {
        alert("Missing application id. No data is loaded.");
        alert("cities.length == 0" + cities.length)
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
function getCitiesJsonPSuccess(data)
{
    jsonData = data;
}

function getCitiesFailure(data, status)
{
    alert("GET CITIES FAILURE:" +status);
}

function getCities(position)
{

}

function getCategoriesAndDatasetsJsonPSuccess(data, status)
{
    var finalDatasets = [];
    console.log("data: ");
    console.log(data);
    console.log("categories:");
    console.log(data.categories);
    console.log("app categories");
    console.log(categories);
        $.each(data.categories, function(j, datasets) {
            console.log("category iteration: ");
            console.log(datasets);
            $.each(datasets, function(j, dataset) {
            if ($.inArray(dataset.category, categories) !== -1) {
                console.log(dataset.id +" is in category: ");
                console.log(dataset.category);
                finalDatasets.push(dataset.id);
            }
            else {
                console.log(dataset.id +" is not in an appropriate category: ");
            }
			});
        });
        console.log("final datasets: ");
        console.log(finalDatasets);
		// if appropriate datasets found, use discovery.php to convert json into list of POIs
        if (finalDatasets.length > 0) {
			$.ajax({
                type: "POST",
                url: "discovery.php",
                data: {uid: callbackAppId, cityId: callbackSelectedCityId, datasetIds: finalDatasets},
                cache: false,
                error: onDatasetFailure  
            }).done(function(data) {
				 //add POIs to application using onDatasetSuccessDiscovery
                 console.log(data);
                 cities.push(citiesJson[callbackShortestIndex]);
                 onDatasetSuccessDiscovery(data);
            });
        }
        else {
            alert("No datasets matching the categories "+categories+" were found for the closest city.")
        }
}

function getDatasetsSuccess(data)
{
    categories_html = "";

    $.each(data.datasets, function(i, dataset) {
        $.each(datasets, function(j, data) {
            if (data.city == cityName)
                datasets_html += "<li class='selectedDataset'><a class='ui-btn ui-btn-icon-right ui-icon-plus' href='#'>" + dataset.title + "</a><span class='datasetId' style='display:none;'>" + dataset.id + "</span><span class='datasetUrl' style='display:none;'>" + dataset.url + "</span></li>";
        });
    });
}
function getAllDatasets()
{
    return 	$.ajax({
        type: "GET",
        url: "http://www.citadelonthemove.eu/DesktopModules/DatasetLibrary/API/Service/GetDatasets?format=json&callback=?",
        cache: false,
        error: onDatasetFailure,
        dataType: "json",
    });
}

function getAppInfo()
{
    $.ajax({
        type: "GET",
        url: "http://localhost/agt-master-discovery/appInfoById.php?format=json&appId=" + appId + "&callback=?",
        cache: false,
        error: onDatasetFailure,
        success: getCitiesJsonPSuccess,
        dataType: "json",
    }).done(function(data) {
        if (data !== null) {
            return data[0].categories;
        }
    }

    )
}
;

function datasetsToArray(allDatasets, selectedCity) {
    var datasets = [];
    var ds = allDatasets[0].datasets;
    for (index = 0; index < ds.length; ++index) {
        if (ds[index].cityId == selectedCity) {
            datasets.push(ds[index].id);
        }
    }
    alert(datasets);
    return datasets;
}

/*Called after a successful dataset fetch*/
function onDatasetSuccessDiscovery(data, status)
{
    console.log("onDatasetSuccessDiscovery() called");
    var startTime = new Date();


    getPoisFromDatasetDiscovery(data);

}

var $loading = $('#loadingDiv').hide();
$(document)
  .ajaxStart(function () {
   $.mobile.loading("show");
  })
  .ajaxStop(function () {
    $.mobile.loading("hide");
  });
  
function discover(position)
{
    var cityAlreadyExists = false;
	// Get dataset categories of current application
    $.ajax({
        type: "GET",
		//local url for development
        //url: "http://localhost/agt-master-discovery/appInfoById.php?format=json&appId=" + appId + "&callback=?",
		url: "http://demos.citadelonthemove.eu/app-generator2/appInfoById.php?format=json&appId=" + appId + "&callback=?",
        cache: false,
        error: onDatasetFailure,
    }).done(function(data) {
        console.log(data);
        //var obj = $.parseJSON(data);
        catNames = data.app[0].categoryNames.split(",");
        
    categories = catNames;
	// Get list of available cities
    $.ajax({
        type: "GET",
		//local url for development
        //url: "http://localhost/agt-master-discovery/cityInfo.php?format=json",
        url: "http://demos.citadelonthemove.eu/app-generator2/cityInfo.php?callback=?",
        cache: false,
        error: getCitiesFailure		
    }).done(function(data) {
	console.log(data);
        jsonData = data;
        console.log("geolocation is supported");
        clearTimeout(location_timeout);
        myLatlng = new google.maps.LatLng(parseFloat(position.coords.latitude), parseFloat(position.coords.longitude));
        citiesJson = jsonData.cities;
        var datasetFound = false;
        // Find the closest city to the user's location
        var p1 = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        var shortestDistance = 999999999999;
        var shortestIndex = 999999999999;
        for (i = 0; i < citiesJson.length; i++) {
            var city = citiesJson[i];
            var p2 = new google.maps.LatLng(city.lat, city.lon);
            var distance = calcDistance(p1, p2);
            if (parseInt(distance, 10) <= parseInt(shortestDistance, 10)) {
                shortestDistance = distance;
                shortestIndex = i;
            }
        }
		// If city is close enough, use it as the active city
        if (shortestDistance < maxCityDistance) {
            var city = citiesJson[shortestIndex];
            var cityId = city.id;
            var cityName = city.name;
            console.log(cities);
            var index;
            for (index = 0; index < cities.length; ++index) {
                var currentCity = cities[index];
		// If app already contains datasets from closest city, end function
				if (currentCity.name === city.name) {
					alert("No data was loaded: this app already contains data from "+city.name);
					cityAlreadyExists = true;
				}
            }
			if (cityAlreadyExists === false) {
				var p2 = new google.maps.LatLng(city.lat, city.lon);
				var distance = calcDistance(p1, p2);
				selectedCityId = cityId;
				selectedCity = cityId; 
				//debug
				console.log(cityId);    
				console.log(cityName);
				console.log("categories: "+categories);
				retrievedCitiesDatasets.push(selectedCityId.toString());
				callbackAppId = appId;
				callbackSelectedCityId = selectedCityId;
				callbackShortestIndex = shortestIndex;
				// Get available datasets from selected city.  
				$.ajax({
					type: "GET",
					url: "http://www.citadelonthemove.eu/DesktopModules/DatasetLibrary/API/Service/GetCityCategoriesAndDatasets?format=json&city="+cityName,
					cache: false,
					// currently throws parsererror but does not affect functionality. This may be due to cross-domain request. Commented out for now.
					//error: getCitiesFailure,
					dataType: "jsonp",
					contentType: "application/json",
				}).done(function(data) {
				// JSONP handler getCategoriesAndDatasetsJsonPSuccess deals with identifying appropriate datasets and calls further functions on success to add them to the application.
						datasetFound = true;
				});
			}
			else {
				alert("city already exists");
			}
			// set current marker
			currentMarker = new google.maps.Marker({
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
    });
    });
}

function showError(error) {
    clearTimeout(location_timeout);
    console.warn('ERROR(' + error.code + '): ' + error.message);

    // $.mobile.hidePageLoadingMsg();
    $.mobile.loading("hide");
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
    setFilters();
}

/* Adds all the markers on the global map object */
function addMarkers()
{
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
    var iw = new google.maps.InfoWindow();
    oms.addListener('click', function(marker) {
        iw.setContent(marker.desc);
        iw.open(map, marker);
        //  console.log(marker);
        currentTransit.lat = marker.position.lat();
        currentTransit.lon = marker.position.lng();
    });

    /* We initialize the infobubble styling */
    infoBubble = new InfoBubble({
        shadowStyle: 1,
        padding: 5,
        backgroundColor: bubbleColor,
        borderRadius: 10,
        arrowSize: 10,
        borderWidth: 1,
        borderColor: '#7c7c7c',
        disableAutoPan: false,
        arrowPosition: 30,
        arrowStyle: 2,
        hideCloseButton: true
    });

    /* For every POI we add a marker with an attached infoBubble */
    var length = pois.length;
    var index = 0;

    var processMarkers = function() {
        for (; index < length; index++) {
            var poi = pois[index];

            if (isFilterSelected(poi.category, poi.cityId) || datasetPreview) {
                /*  posList contains a list of space separated coordinates.
                 *  The first two are lat and lon
                 */
                var coords = poi.location.point.pos.posList.split(" ");
                var current_markerpos = new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1]));

                var marker_image = getFavouriteValue(poi.id) ? "images/star.png" : getMarkerImage(poi.category[0], poi.cityId);
                if (!marker_image)
                    marker_image = "images/pin1.png";
                var current_marker = new google.maps.Marker({
                    position: current_markerpos,
                    map: map,
                    icon: marker_image
                });
                current_marker.citadelPoi = poi;

                markersArray[poi.id] = current_marker;
                oms.addMarker(current_marker);
                google.maps.event.addListener(current_marker, 'click', function() {
                  
                    refreshMap();
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
 
    loadDetailsPage();
    loadListPageData();
    refreshListPageView();
    updateProgressBar('Done', 100);
    $('#progressbar').hide("slow", function() {
    });

    if (datasetPreview)
    {
        google.maps.event.trigger(map, 'resize');
        // map.
        refreshMap();
        var aPoi = markersArray[0];
        mapLat = aPoi.position.lat();
        mapLon = aPoi.position.lng();
        map.setZoom(16);
        map.setCenter(new google.maps.LatLng(mapLat, mapLon));
        // map.panTo(new google.maps.LatLng(mapLat, mapLon));
        // map.panTo();

    }
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
    return "pin1";
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
            "</a><a data-rel='popup' onclick='showTransitOptions()'  data-transition='slideup' class='takeMeThere ui-btn ui-mini ui-corner-all ui-shadow ui-btn-inline ui-icon-navigation ui-btn-icon-left ui-btn-a transitPopup'>Take me there</a></div><div id='bubbleClose'><a href='' onclick='return overrideBubbleCloseClick();'><img src='images/close.png' width='25' height='25' alt='close' /></a></div>";
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
//href='#page1'
    contentTemplate += "<li><a onclick='seeOnMap(); return false;'><img class='seeOnMap' src='images/seeOnMap.png' alt='see POI on map'/></a></li>";
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

        if (isFilterSelected(poi.category, poi.cityId) || datasetPreview) {
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
                    "<img src='images/" + imageClass + ".png'  />" +
                    "<span>" + poi.title + "</span>" +
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
    var startTime = new Date();

    $('#list > ul').html(setListPagePois());

    var endTime = new Date();
    var duration = endTime - startTime; 
}

/* Refreshes the list of POIS in the List Page */
function refreshListPageView()
{   
    var startTime = new Date();

    if ($("#list > ul").hasClass("ui-listview")) {
        $("#list > ul").listview('refresh');
    }

    var endTime = new Date();
    var duration = endTime - startTime;
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
    var startTime = new Date();

    if (pageId != 0) {
        $('#item').html(setDetailPagePoi(pois[pageId]));
        showFavouriteButtons(pageId);
        pageId = 0;
    }
    var endTime = new Date();
    var loadDetailsPageDuration = endTime - startTime;   
}


function seeOnMap()
{
    $.mobile.changePage("#page1", {transition: "none"});
    map.setZoom(16);
    infoBubble.setContent(setInfoWindowPoi(currentPoi));
    infoBubble.open(map, markersArray[currentPoiId]);
//    refreshPoiVotes(currentPoi);
    refreshPoiVotes(currentPoiId);
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
        if(retrievedCitiesDatasets.length > 0)
            {
        if ($('#map-filter').is(":visible")) {
            $('#map-filter').slideUp();
        } else {
            $('#city-filter').slideUp();
            $('#map-filter').slideDown();
        }
        return false;
            }
            else
                {
                
                alert('You have to select a city before you can filter by category');
                }
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


function showTransitOptions()
{
    $("#popupMenu").popup("open");
}

function updateProgressBar(labelText, value) {   
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
    var startTime = new Date();
    getPoisFromDataset(data); 
}

function onDatasetFailure(data, status)
{
    alert('There was a problem fetching the dataset.');
}

/* Sets the available filters  */
function setFilters()
{   
    startTime = new Date();

    /*enable filter selection*/
    $('#filter').removeClass('ui-disabled');

    var length = filters.length;
  
    filters_html = "";
    filtersIndex = 0;
    /*Define processSetFilters function to allow the filters to be processed in batches */
    var processSetFilters = function() {
        for (; filtersIndex < length; filtersIndex++) {
            var filter = filters[filtersIndex];

            if (filter.isVisible) {
                var checked = filter.selected ? ' checked' : '';
                var filterName = filter.name;
                var filterType = filter.type;
                var filterCityId = filter.cityId;
                var filterPoisCounter = filter.poisCounter;
                var filterValue = filterName + "/" + filterCityId;
                filters_html += "<input type='checkbox'" + checked + " name='map-filter' id='map-filter" + filtersIndex + "' class='map-filter' value=\"" + filterValue + "\" />" +
                        "<label for='map-filter" + filtersIndex + "'><img id='img_style' src='images/pin" + filtersIndex % 16 + ".png'/> " + filterName + " , (" + filterPoisCounter + " " + filterType + ")</label>";
            }

            /* Every 20 iterations, we set a 5 second timeout, to prevent the browser from freezing*/
            if (filtersIndex + 1 < length && filtersIndex % 20 === 0) {

                updateProgressBar('Setting Categories...', ((filtersIndex / length) * 30) + 20);

                /* Covers the case where this is also the last iteration */
                if (filtersIndex === length - 1)
                {                  
                    setFiltersCallback();
                }
                else {
                    setTimeout(processSetFilters, 5);
                    filtersIndex++;
                    break;
                }
            }

            // Last iteration
            if (filtersIndex === length - 1)
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
  
    addMarkers();
}

/* Sets the available city filters  */
function setCityFilters()
{    
    var startTime = new Date();
    var filters_html = "";
    for (i = 0; i < cities.length; i++) {
        var city = cities[i];
        var filterName = city.name;
        var filterLat = city.lat;
        var filterLon = city.lon;
        var coords = filterLat + "/" + filterLon;
        filters_html += "<input type='radio' " + " name='city' id='city-filter" + city.id + "' class='city-filter' value=\"" + coords + "\" />" +
                "<label for='city-filter" + city.id  + "'>\n\
    " + filterName + " </label>";
    }

    $('#city-filter > div > fieldset').html(filters_html);
    $('#city-filter > div > fieldset > input').checkboxradio({mini: true});

    var endTime = new Date();
    var duration = endTime - startTime;
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
}

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

            currentMarker.setMap(null);

            var currentmarker = new google.maps.Marker({
                position: currentmarkerpos,
                map: map,
                animation: google.maps.Animation.DROP
            });
            startPoint = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            // startPoint = new google.maps.LatLng( 38.002969, 23.738236);
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

    endPoint = new google.maps.LatLng(currentTransit.lat, currentTransit.lon);

    var request = {
        origin: startPoint,
        destination: endPoint,
        travelMode: google.maps.TravelMode[transitType]
    };
    directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            //    var warnings = document.getElementById('warnings_panel');
            //warnings.innerHTML = '<b>' + response.routes[0].warnings + '</b>';
          
            directionsDisplay.setDirections(response);
            showSteps(response);
            $.mobile.loading("hide");
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
        fixMapHeight();
    });
}

