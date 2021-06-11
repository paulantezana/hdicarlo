document.addEventListener("DOMContentLoaded", () => {
    reportDeliveryList();
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

    reportDeliveryList();
}

function reportDeliveryList(page = 1, limit = 10, search = "") {
    let reportDeliveryTable = document.getElementById("reportDeliveryTable");
    let filterByDate = document.querySelector('input[name="filterByDate"]:checked').value;
    let filterYear = document.getElementById("filterYear").value;
    let filterMonth = document.getElementById("filterMonth").value;
    let filterDay = document.getElementById("filterDay").value;

    if (reportDeliveryTable) {
        SnFreeze.freeze({ selector: "#reportDeliveryTable" });
        RequestApi.fetch(`/admin/report/deliveryReportTable`, {
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
            .then(res => {
                if (res.success) {
                    reportDeliveryTable.innerHTML = res.view;
                } else {
                    SnModal.error({ title: "Algo salió mal", content: res.message });
                }
            })
            .finally((e) => {
                SnFreeze.unFreeze("#reportDeliveryTable");
            });
    }
}

function reportDeliverySetLoading(state) {
    let jsReportDeliveryAction = document.querySelectorAll(".jsReportDeliveryAction");
    if (state) {
        if (jsReportDeliveryAction) {
            jsReportDeliveryAction.forEach((item) => {
                item.setAttribute("disabled", "disabled");
            });
        }
    } else {
        if (jsReportDeliveryAction) {
            jsReportDeliveryAction.forEach((item) => {
                item.removeAttribute("disabled");
            });
        }
    }
}

function reportDeliveryCancel(deliveryId, content = "") {
    SnModal.confirm({
        title: `¿Estás seguro de anular esta orden ${content}?`,
        content: 'Ingrese el motivo por que desea anular este orden <span class="SnTag warning">Esta acción es irreversible</span>',
        input: true,
        okText: "Si",
        okType: "error",
        cancelText: "No",
        onOk(message) {
            reportDeliverySetLoading(true);
            RequestApi.fetch("/admin/report/deliveryCancel", {
                method: "POST",
                body: {
                    deliveryId: deliveryId || 0,
                    message,
                },
            })
                .then((res) => {
                    if (res.success) {
                        SnMessage.success({ content: res.message });
                        reportDeliveryList();
                    } else {
                        SnModal.error({ title: "Algo salió mal", content: res.message });
                    }
                })
                .finally((e) => {
                    reportDeliverySetLoading(false);
                });
        },
    });
}

function reportDeliveryItem(deliveryId) {
    reportDeliverySetLoading(true);
    RequestApi.fetch("/admin/report/deliveryItems", {
        method: "POST",
        body: {
            deliveryId: deliveryId || 0,
        },
    })
        .then((res) => {
            if (res.success) {
                SnModal.open('deliveryItemModalForm');
                document.getElementById('deliveryItemModalBody').innerHTML = res.view;
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            reportDeliverySetLoading(false);
        });
}