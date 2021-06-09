let orderItem = [];
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

    document.getElementById('orderItemTableBody').innerHTML = '';
    orderItem = [];
    calculateTotalOrder();
}

function saveOrder() {
    if (document.getElementById('exhibitorId') == undefined) {
        SnModal.error({ title: 'Algo salió mal', content: 'No se encontró ninguna exibidora' });
        return;
    }
    if (orderItem.length == 0) {
        SnModal.error({ title: 'Algo salió mal', content: 'Ingrese al menos un producto' });
        return;
    }

    let order = {}
    order.code = document.getElementById('searchExhibitorCode').value;
    order.observation = document.getElementById('orderObservation').value;
    order.exhibitorId = document.getElementById('exhibitorId').value;
    order.latitude = document.getElementById('currentLatitude').value;
    order.longitude = document.getElementById('currentLongitude').value;
    order.dateOfDelivery = document.getElementById('orderDateOfDelivery').value;
    order.item = orderItem;
    order.total =  document.getElementById('orderTotal').innerHTML;
    
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

function addOrderItem(productId) {
    let matchProduct = products.find(item => item.product_id == productId);
    let uniqueId = `U${generateUniqueId()}`;
    orderItem.push({
        quantity: 1,
        unitPrice: parseFloat(matchProduct.price),
        total: parseFloat(matchProduct.price),
        productId: parseInt(productId),
        observation: '',
        description: matchProduct.title,
        uniqueId: uniqueId,
    });

    let tableBody = document.getElementById('orderItemTableBody');
    if (tableBody) {
        let orderItemHtml = `<tr id="orderItemRow_${uniqueId}">
                            <td>
                              <div class="SnBtn-group">
                                <button class="SnBtn icon error" onclick="minusQuantityOrderItem('${uniqueId}')"><i class="fas fa-minus"></i></button>
                                <button class="SnBtn icon success" onclick="plusQuantityOrderItem('${uniqueId}')"><i class="fas fa-plus"></i></button>
                              </div>
                            </td>
                            <td>
                              <button class="SnBtn icon primary" onclick="updateOrderItem('${uniqueId}')"><i class="far fa-comment-dots"></i></button>
                            </td>
                            <td>
                              <div>${matchProduct.title} <small id="orderItemObservation_${uniqueId}"></small></div>
                              <small>
                                <span id="orderItemQuantity_${uniqueId}">1</span>
                                <span>Unidad(es) a S/. <span id="orderItemUnitPrice_${uniqueId}">${matchProduct.price}</span></span>
                              </small>
                            </td>
                            <td id="orderItemTotal_${uniqueId}">${matchProduct.price}</td>
                        </tr>`;
        tableBody.insertAdjacentHTML('beforeend', orderItemHtml);
    }

    calculateTotalOrder();
}

function minusQuantityOrderItem(uniqueId) {
    let orderItemQuantity = document.getElementById(`orderItemQuantity_${uniqueId}`);
    let orderItemTotal = document.getElementById(`orderItemTotal_${uniqueId}`);

    orderItem.forEach((item, i) => {
        if (item.uniqueId === uniqueId) {
            orderItem[i].quantity = item.quantity - 1;
            orderItem[i].total = orderItem[i].quantity * orderItem[i].unitPrice;

            if (orderItem[i].quantity > 0) {
                orderItemQuantity.innerHTML = orderItem[i].quantity;
                orderItemTotal.innerHTML = orderItem[i].total;
            } else {
                let orderItemRow = document.getElementById(`orderItemRow_${item.uniqueId}`);
                orderItemRow.remove();
                orderItem.splice(i, 1);
            }
        }
    });

    calculateTotalOrder();
}

function plusQuantityOrderItem(uniqueId) {
    let orderItemQuantity = document.getElementById(`orderItemQuantity_${uniqueId}`);
    let orderItemTotal = document.getElementById(`orderItemTotal_${uniqueId}`);

    orderItem.forEach((item, i) => {
        if (item.uniqueId === uniqueId) {
            orderItem[i].quantity = item.quantity + 1;
            orderItem[i].total = orderItem[i].quantity * orderItem[i].unitPrice;

            orderItemQuantity.innerHTML = orderItem[i].quantity;
            orderItemTotal.innerHTML = orderItem[i].total;
        }
    });

    calculateTotalOrder();
}

function calculateTotalOrder() {
    let total = 0;
    orderItem.forEach(item => {
        total += item.total;
    });

    document.getElementById('orderTotal').innerHTML = total;
}

function updateOrderItem(uniqueId){
    SnModal.open('ordenItemModalForm');
    let matchProduct = orderItem.find(item => item.uniqueId == uniqueId);

    if(matchProduct){
        document.getElementById('ordenItemUniqueId').value = matchProduct.uniqueId;
        document.getElementById('ordenItemObservation').value = matchProduct.observation;
        document.getElementById('ordenItemUnitPrice').value = matchProduct.unitPrice;
    }
}

function ordenItemSubmit(e){
    e.preventDefault();

    let uniqueId = document.getElementById('ordenItemUniqueId').value;
    let observation = document.getElementById('ordenItemObservation').value;
    let unitPrice = document.getElementById('ordenItemUnitPrice').value;

    let orderItemObservation = document.getElementById(`orderItemObservation_${uniqueId}`);
    let orderItemUnitPrice = document.getElementById(`orderItemUnitPrice_${uniqueId}`);
    let orderItemTotal = document.getElementById(`orderItemTotal_${uniqueId}`);

    orderItem.forEach((item, i) => {
        if (item.uniqueId === uniqueId) {
            orderItem[i].unitPrice = unitPrice;
            orderItem[i].observation = observation;
            orderItem[i].total = orderItem[i].quantity * orderItem[i].unitPrice;

            orderItemObservation.innerHTML = `(${orderItem[i].observation})`;
            orderItemUnitPrice.innerHTML = orderItem[i].unitPrice;
            orderItemTotal.innerHTML = orderItem[i].total;

            SnModal.close('ordenItemModalForm');
        }
    });

    calculateTotalOrder();
}