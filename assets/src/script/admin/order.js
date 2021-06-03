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

function clearOrder() {
  document.getElementById('exhibitorDetail').innerHTML = '';
  document.getElementById('searchExhibitorCode').value = '';
  document.getElementById('orderObservation').value = '';
}

function saveOrder() {
  if (document.getElementById('exhibitorId') == undefined) {
    SnModal.error({ title: 'Algo salió mal', content: 'No se encontró ninguna exibidora' });
    return;
  }

  let order = {}
  order.code = document.getElementById('searchExhibitorCode').value;
  order.observation = document.getElementById('orderObservation').value;
  order.exhibitorId = document.getElementById('exhibitorId').value;
  order.latitude = document.getElementById('currentLatitude').value;
  order.longitude = document.getElementById('currentLongitude').value;
  order.dateOfDelivery = document.getElementById('orderDateOfDelivery').value;

  SnModal.confirm({
    title: "¿Estás seguro de continuar con el registro?",
    content: '',
    okText: "Si",
    okType: "error",
    cancelText: "No",
    onOk() {
      exhibitorSetLoading(true);
      RequestApi.fetch("/admin/order/save", {
        method: "POST",
        body: order,
      })
        .then((res) => {
          if (res.success) {
            SnMessage.success({ content: res.message });
            clearOrder();
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