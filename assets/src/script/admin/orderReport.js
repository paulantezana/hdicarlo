document.addEventListener("DOMContentLoaded", () => {
    reportOrderList();
});

function reportOrderList(page = 1, limit = 10, search = "") {
    let reportOrderTable = document.getElementById("reportOrderTable");
    if (reportOrderTable) {
        SnFreeze.freeze({ selector: "#reportOrderTable" });
        RequestApi.fetch(`/admin/report/orderReportTable?limit=${limit}&page=${page}&search=${search}`,{
            method: "GET",
        })
            .then((res) => {
                if (res.success) {
                    reportOrderTable.innerHTML = res.view;
                } else {
                    SnModal.error({ title: "Algo salió mal", content: res.message });
                }
            })
            .finally((e) => {
                SnFreeze.unFreeze("#reportOrderTable");
            });
    }
}

function reportOrderSetLoading(state) {
    let jsReportOrderAction = document.querySelectorAll(".jsReportOrderAction");
    if (state) {
        if (jsReportOrderAction) {
            jsReportOrderAction.forEach((item) => {
                item.setAttribute("disabled", "disabled");
            });
        }
    } else {
        if (jsReportOrderAction) {
            jsReportOrderAction.forEach((item) => {
                item.removeAttribute("disabled");
            });
        }
    }
}

function reportOrderCancel(orderId, content = "") {
    SnModal.confirm({
        title: `¿Estás seguro de anular esta orden ${content}?`,
        content: 'Ingrese el motivo por que desea anular este orden <span class="SnTag warning">Esta acción es irreversible</span>',
        input: true,
        okText: "Si",
        okType: "error",
        cancelText: "No",
        onOk(message) {
            reportOrderSetLoading(true);
            RequestApi.fetch("/admin/report/orderCancel", {
                method: "POST",
                body: {
                    orderId: orderId || 0,
                    message,
                },
            })
                .then((res) => {
                    if (res.success) {
                        SnMessage.success({ content: res.message });
                        reportOrderList();
                    } else {
                        SnModal.error({ title: "Algo salió mal", content: res.message });
                    }
                })
                .finally((e) => {
                    reportOrderSetLoading(false);
                });
        },
    });
}

function reportOrderItem(orderId){
    reportOrderSetLoading(true);
    RequestApi.fetch("/admin/report/orderItems", {
        method: "POST",
        body: {
            orderId: orderId || 0,
        },
    })
        .then((res) => {
            if (res.success) {
                SnModal.open('orderItemModalForm');
                document.getElementById('orderItemModalBody').innerHTML = res.view;
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            reportOrderSetLoading(false);
        });
}