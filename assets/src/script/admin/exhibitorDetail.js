let exhibitorGlobalMap;
let exhibitorGlobalMarker;

class InfinityLoading {
    constructor(container, dataSend) {
        this.loading = false;
        this.more = true;
        this.current = 0;
        this.container = document.querySelector(container);
        this.dataSend = dataSend;
        this.listener();
    }

    reload() {
        this.loading = false;
        this.more = true;
        this.current = 0;
        if (this.container) {
            this.container.innerHTML = "";
            this.loadMore();
        }
    }

    listener() {
        // first loading
        this.loadMore();

        // validate
        if (this.container) {
            this.container.addEventListener("scroll", (ev) => {
                if (
                    this.container.scrollTop + this.container.clientHeight >=
                    this.container.scrollHeight - 50
                ) {
                    this.loadMore();
                }
            });
        }
    }

    loadingState() {
        if (this.container) {
            let loader = this.container.nextElementSibling;
            if (loader) {
                if (this.loading) {
                    loader.classList.add("visible");
                    return;
                }
                loader.classList.remove("visible");
            }
        }
    }

    async loadMore() {
        // validate
        if (this.loading || !this.more) {
            return;
        }

        // change state
        this.loading = true;
        this.more = false;

        // validate
        if (this.container) {
            // call loading
            this.loadingState();

            // init fetch
            await RequestApi.fetch("/admin/exhibitor/states", {
                method: "POST",
                body: {
                    ...this.dataSend,
                    current: this.current + 1,
                },
            })
                .then((response) => {
                    if (response.success) {
                        let currentData = response.result.data;
                        this.current = response.result.current;
                        this.more = response.result.more;

                        currentData.forEach((item) => {
                            let htmlTemplte = `<div>
                                                    <strong class="SnMr-1">${item.exhibitor_state}</strong>
                                                    <small>${item.time_of_issue}</small>
                                                    <div>
                                                        <span>${item.full_name}</span>
                                                    </div>
                                                </div>`;
                            this.container.insertAdjacentHTML('beforeend',htmlTemplte);
                        });
                    } else {
                        SnModal.error({
                            title: "Algo salio mal!",
                            content: response.message,
                        });
                    }
                })
                .finally(() => {
                    this.loading = false;
                    this.loadingState();
                });
        }
    }
}


document.addEventListener("DOMContentLoaded", () => {
    exhibitorSetPositionMpas();
    let exhibitorId = document.getElementById('exhibitorId').value
    new InfinityLoading("#exhibitorStates",{
        exhibitorId
    });
});

function initGoogleMaps() {
    geoGetCurrentPosition().then(userLocation => {
        drawGoogleMapExhibitorGlobal(userLocation);
    }).catch(err => SnMessage.error({ content: err }));
}

function drawGoogleMapExhibitorGlobal(userLocation) {
    exhibitorGlobalMap = new google.maps.Map(document.getElementById('exhibitorMap'), {
        center: userLocation,
        zoom: 17,
        rotateControl: false,
        fullscreenControl: false,
        streetViewControl: false,
        mapTypeControl: false,
    });

    exhibitorGlobalMarker = new google.maps.Marker({
        position: userLocation,
        draggable: true,
        animation: google.maps.Animation.DROP,
        map: exhibitorGlobalMap
    });
}

function setPositionMarkerGlobal(location) {
    let setPositionInterval = setInterval(() => {
        if (exhibitorGlobalMap && exhibitorGlobalMarker) {
            clearInterval(setPositionInterval);
            let newLatLng = new google.maps.LatLng(location.lat, location.lng);
            exhibitorGlobalMap.setCenter(newLatLng);
            exhibitorGlobalMap.setZoom(17);
            exhibitorGlobalMarker.setPosition(newLatLng);
        }
    }, 1000);
}


function exhibitorSetPositionMpas() {
    let location = document.getElementById('exhibitorLatLong').value;
    let latLong = location.split(',');
    if (latLong.length > 0) {
        let latitude = latLong[0];
        let longitude = latLong[1];

        setPositionMarkerGlobal({
            lat: latitude,
            lng: longitude,
        });
    } else {
        SnMessage.error({ content: 'Ubicación mal establecida' });
    }
}