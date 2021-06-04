let exhibitorState = {
  modalType: "create",
  modalName: "exhibitorModalForm",
  loading: false,
  slimCustomerId: null,
  slimGeoLocation: null,
};
let pValidator;
let map;
let marker;

let customerState = {
  modalType: "create",
  modalName: "customerModalForm",
  loading: false,
};
let customerPValidator;

let exhibitorGlobalMap;
let exhibitorGlobalMarker;

document.addEventListener("DOMContentLoaded", () => {
  pValidator = new Pristine(document.getElementById("exhibitorForm"));
  customerPValidator = new Pristine(document.getElementById("customerForm"));

  document.getElementById("searchContent").addEventListener("input", (e) => {
    exhibitorList(1, 10, e.target.value);
  });

  exhibitorList();

  exhibitorState.slimCustomerId = new SlimSelect({
    select: '#exhibitorCustomerId',
    searchingText: 'Buscando...',
    // addToBody: true,
    ajax: function (search, callback) {
      if (search.length < 2) {
        callback('Escriba almenos 2 caracteres');
        return
      }
      RequestApi.fetch('/admin/customer/searchBySocialReason', {
        method: 'POST',
        body: { search }
      }).then(res => {
        if (res.success) {
          let data = res.result.map(item => ({ text: item.social_reason, value: item.customer_id }));
          callback(data);
        } else {
          callback(false);
        }
      }).catch(err => {
        callback(false);
      })
    }
  });

  exhibitorState.slimGeoLocation = new SlimSelect({
    select: '#exhibitorGeoLocationId',
    searchingText: 'Buscando...',
    // addToBody: true,
    ajax: function (search, callback) {
      if (search.length < 2) {
        callback('Escriba almenos 2 caracteres');
        return
      }
      RequestApi.fetch('/page/searchLocationLastLevel', {
        method: 'POST',
        body: { search }
      }).then(res => {
        if (res.success) {
          let data = res.result.map(item => ({ text: item.geo_name, value: item.geo_location_id }));
          callback(data);
        } else {
          callback(false);
        }
      }).catch(err => {
        callback(false);
      })
    }
  });

  // FILETER
  new SlimSelect({
    select: '#filterCustomerId',
    searchingText: 'Buscando...',
    // addToBody: true,
    ajax: function (search, callback) {
      if (search.length < 2) {
        callback('Escriba almenos 2 caracteres');
        return
      }
      RequestApi.fetch('/admin/customer/searchBySocialReason', {
        method: 'POST',
        body: { search }
      }).then(res => {
        if (res.success) {
          let data = res.result.map(item => ({ text: item.social_reason, value: item.customer_id }));
          callback(data);
        } else {
          callback(false);
        }
      }).catch(err => {
        callback(false);
      })
    }
  });
});

// FILTER
function exhibitorSetLoading(state) {
  exhibitorState.loading = state;
  let jsExhibitorAction = document.querySelectorAll(".jsExhibitorAction");
  let submitButton = document.getElementById("exhibitorFormSubmit");
  if (exhibitorState.loading) {
    if (submitButton) {
      submitButton.setAttribute("disabled", "disabled");
      submitButton.classList.add("loading");
    }
    if (jsExhibitorAction) {
      jsExhibitorAction.forEach((item) => {
        item.setAttribute("disabled", "disabled");
      });
    }
  } else {
    if (submitButton) {
      submitButton.removeAttribute("disabled");
      submitButton.classList.remove("loading");
    }
    if (jsExhibitorAction) {
      jsExhibitorAction.forEach((item) => {
        item.removeAttribute("disabled");
      });
    }
  }
}

function exhibitorList(page = 1, limit = 10, search = "") {
  let exhibitorTable = document.getElementById("exhibitorTable");
  if (exhibitorTable) {
    SnFreeze.freeze({ selector: "#exhibitorTable" });
    RequestApi.fetch(
      `/admin/exhibitor/table?limit=${limit}&page=${page}&search=${search}`,
      {
        method: "GET",
      }
    )
      .then((res) => {
        if (res.success) {
          exhibitorTable.innerHTML = res.view;
          SnDropdown();
        } else {
          SnModal.error({ title: "Algo salió mal", content: res.message });
        }
      })
      .finally((e) => {
        SnFreeze.unFreeze("#exhibitorTable");
      });
  }
}

function exhibitorClearForm() {
  let currentForm = document.getElementById("exhibitorForm");
  let exhibitorCode = document.getElementById("exhibitorCode");
  if (currentForm && exhibitorCode) {
    geoGetCurrentPosition().then(userLocation => {
      drawGoogleMap(userLocation);
    }).catch(err => toastr.error(err, 'Algo salió mal!!'));
    currentForm.reset();
    exhibitorCode.focus();
  }
  pValidator.reset();
}

function exhibitorSubmit(e) {
  e.preventDefault();
  if (!pValidator.validate()) {
    return;
  }
  exhibitorSetLoading(true);

  let exhibitorSendData = {};
  exhibitorSendData.code = document.getElementById('exhibitorCode').value;
  exhibitorSendData.sizeId = document.getElementById('exhibitorSizeId').value;
  exhibitorSendData.customerId = document.getElementById('exhibitorCustomerId').value;
  exhibitorSendData.geoLocationId = document.getElementById('exhibitorGeoLocationId').value;
  exhibitorSendData.address = document.getElementById('exhibitorAddress').value;
  exhibitorSendData.latitude = document.getElementById('exhibitorLatitude').value;
  exhibitorSendData.longitude = document.getElementById('exhibitorLongitude').value;

  if (exhibitorState.modalType === "update") {
    exhibitorSendData.exhibitorId = document.getElementById("exhibitorId").value || 0;
  }

  RequestApi.fetch('/admin/exhibitor/' + exhibitorState.modalType, {
    method: "POST",
    body: exhibitorSendData,
  })
    .then((res) => {
      if (res.success) {
        SnModal.close(exhibitorState.modalName);
        SnMessage.success({ content: res.message });
        exhibitorList();
      } else {
        SnModal.error({ title: "Algo salió mal", content: res.message });
      }
    })
    .finally((e) => {
      exhibitorSetLoading(false);
    });
}

function exhibitorDelete(exhibitorId, content = "") {
  SnModal.confirm({
    title: "¿Estás seguro de eliminar este registro?",
    content: content,
    okText: "Si",
    okType: "error",
    cancelText: "No",
    onOk() {
      exhibitorSetLoading(true);
      RequestApi.fetch("/admin/exhibitor/delete", {
        method: "POST",
        body: {
          exhibitorId: exhibitorId || 0,
        },
      })
        .then((res) => {
          if (res.success) {
            SnMessage.success({ content: res.message });
            exhibitorList();
          } else {
            SnModal.error({ title: "Algo salió mal", content: res.message });
          }
        })
        .finally((e) => {
          exhibitorSetLoading(false);
        });
    },
  });
}

function exhibitorShowModalCreate() {
  exhibitorState.modalType = "create";
  exhibitorClearForm();
  SnModal.open(exhibitorState.modalName);
}

function exhibitorShowModalUpdate(exhibitorId) {
  exhibitorState.modalType = "update";
  exhibitorGetById(exhibitorId);
}

function exhibitorGetById(exhibitorId) {
  exhibitorClearForm();
  exhibitorSetLoading(true);

  RequestApi.fetch("/admin/exhibitor/id", {
    method: "POST",
    body: {
      exhibitorId: exhibitorId || 0,
    },
  })
    .then((res) => {
      if (res.success) {
        document.getElementById('exhibitorId').value = res.result.exhibitor_id;
        document.getElementById('exhibitorCode').value = res.result.code;
        document.getElementById('exhibitorSizeId').value = res.result.size_id;
        if (res.result.lat_long.length > 2) {
          let latLong = res.result.lat_long.split(',');
          let latitude = latLong[0];
          let longitude = latLong[1];
          document.getElementById('exhibitorLatitude').value = latitude;
          document.getElementById('exhibitorLongitude').value = longitude;
          document.getElementById('exhibitorAddress').value = res.result.address;
          setPositionMarker({
            lat: latitude,
            lng: longitude,
          });
        }

        exhibitorState.slimCustomerId.setData([
          {
            text: res.result.customer_social_reason,
            value: res.result.customer_id,
          },
        ]);
        exhibitorState.slimCustomerId.set(res.result.customer_id);

        exhibitorState.slimGeoLocation.setData([
          {
            text: res.result.geo_name,
            value: res.result.geo_location_id,
          },
        ]);
        exhibitorState.slimGeoLocation.set(res.result.geo_location_id);

        SnModal.open(exhibitorState.modalName);
      } else {
        SnModal.error({ title: "Algo salió mal", content: res.message });
      }
    })
    .finally((e) => {
      exhibitorSetLoading(false);
    });
}

function exhibitorToExcel() {
  let dataTable = document.getElementById("exhibitorCurrentTable");
  if (dataTable) {
    TableToExcel(dataTable.outerHTML, 'Usuario', 'Usuario');
  }
}

function exhibitorToPrint() {
  printArea("exhibitorCurrentTable");
}



// CUSTOMER
function customerSetLoading(state) {
  customerState.loading = state;
  let jsCustomerAction = document.querySelectorAll(".jsCustomerAction");
  let submitButton = document.getElementById("customerFormSubmit");
  if (customerState.loading) {
    if (submitButton) {
      submitButton.setAttribute("disabled", "disabled");
      submitButton.classList.add("loading");
    }
    if (jsCustomerAction) {
      jsCustomerAction.forEach((item) => {
        item.setAttribute("disabled", "disabled");
      });
    }
  } else {
    if (submitButton) {
      submitButton.removeAttribute("disabled");
      submitButton.classList.remove("loading");
    }
    if (jsCustomerAction) {
      jsCustomerAction.forEach((item) => {
        item.removeAttribute("disabled");
      });
    }
  }
}

function customerShowModalCreate() {
  customerState.modalType = "create";
  customerClearForm();
  SnModal.open(customerState.modalName);
}

function customerClearForm() {
  let currentForm = document.getElementById("customerForm");
  let customerEmail = document.getElementById("customerEmail");
  if (currentForm && customerEmail) {
    currentForm.reset();
    customerEmail.focus();
  }
  customerPValidator.reset();
}

function customerSubmit(e) {
  e.preventDefault();
  if (!customerPValidator.validate()) {
    return;
  }
  customerSetLoading(true);

  let customerSendData = {};
  customerSendData.identityDocumentCode = document.getElementById("customerIdentityDocumentCode").value;
  customerSendData.documentNumber = document.getElementById("customerDocumentNumber").value;
  customerSendData.socialReason = document.getElementById("customerSocialReason").value;
  customerSendData.commercialReason = document.getElementById("customerCommercialReason").value;
  customerSendData.fiscalAddress = document.getElementById("customerFiscalAddress").value;
  customerSendData.email = document.getElementById("customerEmail").value;
  customerSendData.telephone = document.getElementById("customerTelephone").value;

  if (customerState.modalType === "update") {
    customerSendData.customerId = document.getElementById("customerId").value || 0;
  }

  RequestApi.fetch('/admin/customer/' + customerState.modalType, {
    method: "POST",
    body: customerSendData,
  })
    .then((res) => {
      if (res.success) {
        SnModal.close(customerState.modalName);
        SnMessage.success({ content: res.message });
        exhibitorState.slimCustomerId.setData([
          {
            text: customerSendData.socialReason,
            value: res.result,
          },
        ]);
        exhibitorState.slimCustomerId.set(res.result);
      } else {
        SnModal.error({ title: "Algo salió mal", content: res.message });
      }
    })
    .finally((e) => {
      customerSetLoading(false);
    });
}



// GOOGLE MAPS
function initGoogleMaps() {
  geoGetCurrentPosition().then(userLocation => {
    drawGoogleMap(userLocation);
    drawGoogleMapExhibitorGlobal(userLocation);
  }).catch(err => SnMessage.error({ content: err }));
}



function drawGoogleMap(userLocation) {
  map = new google.maps.Map(document.getElementById('googleMap'), {
    center: userLocation,
    zoom: 17,
    rotateControl: false,
    fullscreenControl: false,
    streetViewControl: false,
    mapTypeControl: false,
  });

  marker = new google.maps.Marker({
    position: userLocation,
    draggable: true,
    animation: google.maps.Animation.DROP,
    map: map
  });

  google.maps.event.addListener(marker, 'dragend', function () {
    let newPosition = {
      lat: marker.position.lat(),
      lng: marker.position.lng(),
    }

    document.getElementById('exhibitorLatitude').value = newPosition.lat;
    document.getElementById('exhibitorLongitude').value = newPosition.lng;

    let geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'location': newPosition }, function (results, status) {
      if (status === 'OK') {
        if (results[0]) {
          $('#exhibitorAddress').val(results[0].formatted_address);
        } else {
          SnMessage.error({ content: 'No se han encontrado resultados: (Google maps)' });
        }
      } else {
        SnMessage.error({ content: 'Geocoder falló debido a: (Google maps)' });
      }
    });
  });


  // Autocomplete
  let input = document.getElementById('exhibitorAddress');
  let autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);
  autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);

  autocomplete.addListener('place_changed', function () {
    let place = autocomplete.getPlace();
    if (!place.geometry) {
      SnMessage.error({ content: `No hay detalles disponibles para la entrada: '${place.name}': (Google maps)` });
      return;
    }
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);
    }
    marker.setPosition(place.geometry.location);

    document.getElementById('exhibitorLatitude').value = place.geometry.location.lat();
    document.getElementById('exhibitorLongitude').value = place.geometry.location.lng();
  });
}

function setPositionMarker(location) {
  let setPositionInterval = setInterval(() => {
    if (map && marker) {
      clearInterval(setPositionInterval);
      let newLatLng = new google.maps.LatLng(location.lat, location.lng);
      map.setCenter(newLatLng);
      map.setZoom(17);
      marker.setPosition(newLatLng);
    }
  }, 1000);
}

// GLOBAL
function drawGoogleMapExhibitorGlobal(userLocation) {
  exhibitorGlobalMap = new google.maps.Map(document.getElementById('exhibitorGlobalMap'), {
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

// MAINTENANCE
function exhibitorMaintenanceShowModal() {
  SnModal.open('exhibitorMaintenanceModalForm');
}

function exhibitorSetPositionMpas(location) {
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