let exhibitorMap;

function initGoogleMaps() {
    geoGetCurrentPosition().then(userLocation => {
        drawGoogleMapExhibitorGlobal(userLocation);
    }).catch(err => SnMessage.error({ content: err }));
}

function drawGoogleMapExhibitorGlobal(userLocation) {
    exhibitorMap = new google.maps.Map(document.getElementById('exhibitorMap'), {
        center: userLocation,
        zoom: 17,
        rotateControl: false,
        fullscreenControl: false,
        streetViewControl: false,
        mapTypeControl: false,
    });
}