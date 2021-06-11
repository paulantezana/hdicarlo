let deliveryItem = [];
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
  document.getElementById('deliveryObservation').value = '';

  document.getElementById('deliveryItemTableBody').innerHTML = '';
  deliveryItem = [];
  calculateTotalDelivery();
}

function saveDelivery() {
  if (document.getElementById('exhibitorId') == undefined) {
    SnModal.error({ title: 'Algo salió mal', content: 'No se encontró ninguna exibidora' });
    return;
  }
  if (deliveryItem.length == 0) {
    SnModal.error({ title: 'Algo salió mal', content: 'Ingrese al menos un producto' });
    return;
}

  let delivery = {}
  delivery.exhibitorId = document.getElementById('exhibitorId').value;
  delivery.code = document.getElementById('searchExhibitorCode').value;
  delivery.observation = document.getElementById('deliveryObservation').value;
  delivery.latitude = document.getElementById('currentLatitude').value;
  delivery.longitude = document.getElementById('currentLongitude').value;
  delivery.item = deliveryItem;
  delivery.total =  document.getElementById('deliveryTotal').innerHTML;

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

function addDeliveryItem(productId) {
  let matchProduct = products.find(item => item.product_id == productId);
  let uniqueId = `U${generateUniqueId()}`;
  deliveryItem.push({
      quantity: 1,
      unitPrice: parseFloat(matchProduct.price),
      total: parseFloat(matchProduct.price),
      productId: parseInt(productId),
      observation: '',
      description: matchProduct.title,
      uniqueId: uniqueId,
  });

  let tableBody = document.getElementById('deliveryItemTableBody');
  if (tableBody) {
      let deliveryItemHtml = `<tr id="deliveryItemRow_${uniqueId}">
                          <td>
                            <div class="SnBtn-group">
                              <button class="SnBtn icon error" onclick="minusQuantityDeliveryItem('${uniqueId}')"><i class="fas fa-minus"></i></button>
                              <button class="SnBtn icon success" onclick="plusQuantityDeliveryItem('${uniqueId}')"><i class="fas fa-plus"></i></button>
                            </div>
                          </td>
                          <td>
                            <button class="SnBtn icon primary" onclick="updateDeliveryItem('${uniqueId}')"><i class="far fa-comment-dots"></i></button>
                          </td>
                          <td>
                            <div>${matchProduct.title} <small id="deliveryItemObservation_${uniqueId}"></small></div>
                            <small>
                              <span id="deliveryItemQuantity_${uniqueId}">1</span>
                              <span>Unidad(es) a S/. <span id="deliveryItemUnitPrice_${uniqueId}">${matchProduct.price}</span></span>
                            </small>
                          </td>
                          <td id="deliveryItemTotal_${uniqueId}">${matchProduct.price}</td>
                      </tr>`;
      tableBody.insertAdjacentHTML('beforeend', deliveryItemHtml);
  }

  calculateTotalDelivery();
}

function minusQuantityDeliveryItem(uniqueId) {
  let deliveryItemQuantity = document.getElementById(`deliveryItemQuantity_${uniqueId}`);
  let deliveryItemTotal = document.getElementById(`deliveryItemTotal_${uniqueId}`);

  deliveryItem.forEach((item, i) => {
      if (item.uniqueId === uniqueId) {
          deliveryItem[i].quantity = item.quantity - 1;
          deliveryItem[i].total = deliveryItem[i].quantity * deliveryItem[i].unitPrice;

          if (deliveryItem[i].quantity > 0) {
              deliveryItemQuantity.innerHTML = deliveryItem[i].quantity;
              deliveryItemTotal.innerHTML = deliveryItem[i].total;
          } else {
              let deliveryItemRow = document.getElementById(`deliveryItemRow_${item.uniqueId}`);
              deliveryItemRow.remove();
              deliveryItem.splice(i, 1);
          }
      }
  });

  calculateTotalDelivery();
}

function plusQuantityDeliveryItem(uniqueId) {
  let deliveryItemQuantity = document.getElementById(`deliveryItemQuantity_${uniqueId}`);
  let deliveryItemTotal = document.getElementById(`deliveryItemTotal_${uniqueId}`);

  deliveryItem.forEach((item, i) => {
      if (item.uniqueId === uniqueId) {
          deliveryItem[i].quantity = item.quantity + 1;
          deliveryItem[i].total = deliveryItem[i].quantity * deliveryItem[i].unitPrice;

          deliveryItemQuantity.innerHTML = deliveryItem[i].quantity;
          deliveryItemTotal.innerHTML = deliveryItem[i].total;
      }
  });

  calculateTotalDelivery();
}

function calculateTotalDelivery() {
  let total = 0;
  deliveryItem.forEach(item => {
      total += item.total;
  });

  document.getElementById('deliveryTotal').innerHTML = total;
}

function updateDeliveryItem(uniqueId){
  SnModal.open('deliveryItemModalForm');
  let matchProduct = deliveryItem.find(item => item.uniqueId == uniqueId);

  if(matchProduct){
      document.getElementById('deliveryItemUniqueId').value = matchProduct.uniqueId;
      document.getElementById('deliveryItemObservation').value = matchProduct.observation;
      document.getElementById('deliveryItemUnitPrice').value = matchProduct.unitPrice;
  }
}

function deliveryItemSubmit(e){
  e.preventDefault();

  let uniqueId = document.getElementById('deliveryItemUniqueId').value;
  let observation = document.getElementById('deliveryItemObservation').value;
  let unitPrice = document.getElementById('deliveryItemUnitPrice').value;

  let deliveryItemObservation = document.getElementById(`deliveryItemObservation_${uniqueId}`);
  let deliveryItemUnitPrice = document.getElementById(`deliveryItemUnitPrice_${uniqueId}`);
  let deliveryItemTotal = document.getElementById(`deliveryItemTotal_${uniqueId}`);

  deliveryItem.forEach((item, i) => {
      if (item.uniqueId === uniqueId) {
          deliveryItem[i].unitPrice = unitPrice;
          deliveryItem[i].observation = observation;
          deliveryItem[i].total = deliveryItem[i].quantity * deliveryItem[i].unitPrice;

          deliveryItemObservation.innerHTML = `(${deliveryItem[i].observation})`;
          deliveryItemUnitPrice.innerHTML = deliveryItem[i].unitPrice;
          deliveryItemTotal.innerHTML = deliveryItem[i].total;

          SnModal.close('deliveryItemModalForm');
      }
  });

  calculateTotalDelivery();
}