document.addEventListener("DOMContentLoaded", () => {
    reportOrderList();
});

function filterByDateChange() {
    let filterByDate = document.querySelector('input[name="filterByDate"]:checked').value;
    let filterYearWrapper = document.getElementById('filterYearWrapper');
    let filterMonthWrapper = document.getElementById('filterMonthWrapper');
    let filterDayWrapper = document.getElementById('filterDayWrapper');

    filterYearWrapper.classList.add('hidden');
    filterMonthWrapper.classList.add('hidden');
    filterDayWrapper.classList.add('hidden');

    if (filterByDate == 1) {
        filterYearWrapper.classList.remove('hidden');
    } else if (filterByDate == 2) {
        filterMonthWrapper.classList.remove('hidden');
    } else if (filterByDate == 3) {
        filterDayWrapper.classList.remove('hidden');
    }

    reportOrderList();
}

function reportOrderList(page = 1, limit = 10, search = "") {
    let reportOrderTable = document.getElementById("reportOrderTable");
    let filterByDate = document.querySelector('input[name="filterByDate"]:checked').value;
    let filterYear = document.getElementById("filterYear").value;
    let filterMonth = document.getElementById("filterMonth").value;
    let filterDay = document.getElementById("filterDay").value;
    
    if (reportOrderTable) {
        SnFreeze.freeze({ selector: "#reportOrderTable" });
        RequestApi.fetch('/admin/report/orderReportTable',{
            method: "POST",
            body: {
                limit,
                page,
                search,
                filterByDate,
                filterYear,
                filterMonth,
                filterDay
            }
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