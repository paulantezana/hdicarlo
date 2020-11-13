document.addEventListener("DOMContentLoaded", () => {
  let searchExhibitorCode = document.getElementById('searchExhibitorCode');
  if (searchExhibitorCode) {
    searchExhibitorCode.addEventListener('keyup', e => {
      if (e.code == 'Enter') {
        searchExhibitorByCode();
      }
    });
  }

  geoGetCurrentPosition().then(userLocation => {
    document.getElementById('currentLatitude').value = userLocation.lat;
    document.getElementById('currentLongitude').value = userLocation.lng;
  }).catch(err => SnMessage.error({ content: err }));
});

function exhibitorSetLoading(loading) {
  let jsUserAction = document.querySelectorAll(".jsUserAction");
  if (loading) {
    if (jsUserAction) {
      jsUserAction.forEach((item) => {
        item.setAttribute("disabled", "disabled");
      });
    }
  } else {
    if (jsUserAction) {
      jsUserAction.forEach((item) => {
        item.removeAttribute("disabled");
      });
    }
  }
}

function searchExhibitorByCode() {
  let code = document.getElementById('searchExhibitorCode').value;

  exhibitorSetLoading(true);
  RequestApi.fetch("/admin/exhibitor/getByCode", {
    method: "POST",
    body: { code },
  })
    .then((res) => {
      if (res.success) {
        document.getElementById('exhibitorDetail').innerHTML = res.view;
      } else {
        SnModal.error({ title: "Algo salió mal", content: res.message });
      }
    })
    .finally((e) => {
      exhibitorSetLoading(false);
    });
}

function clearDelivery() {
  document.getElementById('exhibitorDetail').innerHTML = '';
  document.getElementById('searchExhibitorCode').value = '';
  document.getElementById('orderDateOfDelivery').value = '';
  document.getElementById('deliveryObservation').value = '';
}

function saveDelivery() {
  if (document.getElementById('exhibitorId') == undefined) {
    SnModal.error({ title: 'Algo salió mal', content: 'No se encontró ninguna exibidora' });
    return;
  }

  let delivery = {}
  delivery.exhibitorId = document.getElementById('exhibitorId').value;
  delivery.code = document.getElementById('searchExhibitorCode').value;
  delivery.observation = document.getElementById('deliveryObservation').value;
  delivery.latitude = document.getElementById('currentLatitude').value;
  delivery.longitude = document.getElementById('currentLongitude').value;

  SnModal.confirm({
    title: "¿Estás seguro de continuar con el registro?",
    content: '',
    okText: "Si",
    okType: "error",
    cancelText: "No",
    onOk() {
      exhibitorSetLoading(true);
      RequestApi.fetch("/admin/delivery/save", {
        method: "POST",
        body: delivery,
      })
        .then((res) => {
          if (res.success) {
            SnMessage.success({ content: res.message });
            clearDelivery();
          } else {
            SnModal.error({ title: 'Algo salió mal', content: res.message });
          }
        })
        .finally((e) => {
          exhibitorSetLoading(false);
        });
    }
  });

}
