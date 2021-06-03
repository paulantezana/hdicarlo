let appPlanState = {
    modalType: "create",
    modalName: "appPlanModalForm",
    loading: false,
};
let pValidator;

function appPlanSetLoading(state) {
    appPlanState.loading = state;
    let jsAppPlanAction = document.querySelectorAll(".jsAppPlanAction");
    let submitButton = document.getElementById("appPlanFormSubmit");
    if (appPlanState.loading) {
        if (submitButton) {
            submitButton.setAttribute("disabled", "disabled");
            submitButton.classList.add("loading");
        }
        if (jsAppPlanAction) {
            jsAppPlanAction.forEach((item) => {
                item.setAttribute("disabled", "disabled");
            });
        }
    } else {
        if (submitButton) {
            submitButton.removeAttribute("disabled");
            submitButton.classList.remove("loading");
        }
        if (jsAppPlanAction) {
            jsAppPlanAction.forEach((item) => {
                item.removeAttribute("disabled");
            });
        }
    }
}

function appPlanList(page = 1, limit = 20, search = "") {
    let appPlanTable = document.getElementById("appPlanTable");
    if (appPlanTable) {
        SnFreeze.freeze({ selector: "#appPlanTable" });
        RequestApi.fetch(
            `/inner/appPlan/table?limit=${limit}&page=${page}&search=${search}`,
            {
                method: "GET",
            }
        )
            .then((res) => {
                if (res.success) {
                    appPlanTable.innerHTML = res.view;
                } else {
                    SnModal.error({ title: "Algo salió mal", content: res.message });
                }
            })
            .finally((e) => {
                SnFreeze.unFreeze("#appPlanTable");
            });
    }
}

function appPlanClearForm() {
    let currentForm = document.getElementById("appPlanForm");
    let appPlanDescripcion = document.getElementById("appPlanDescripcion");
    let appPlanIntervalTableBody = document.getElementById("appPlanIntervalTableBody");
    if (currentForm) {
        currentForm.reset();
    }
    if (appPlanDescripcion) {
        setTimeout(() => {
            appPlanDescripcion.focus();
        }, 500)
    }
    if (appPlanIntervalTableBody) {
        appPlanIntervalTableBody.innerHTML = '';
    }
    pValidator.reset();
}

function appPlanSubmit(e) {
    e.preventDefault();
    if (!pValidator.validate()) {
        return;
    }
    appPlanSetLoading(true);

    let appPlanSendData = {};
    appPlanSendData.description = document.getElementById("appPlanDescripcion").value;
    
    let table = document.getElementById('appPlanIntervalTableBody');
    appPlanSendData.interval = [...table.children].map((row, index) => {
        let uniqueId = row.dataset.uniqueid;
        let isnew = row.dataset.isnew;

        let appPaymentIntervalId = document.getElementById(`appPlanIntervalProcessTypeId_${uniqueId}`).value;
        let price = document.getElementById(`appPlanIntervalPrice_${uniqueId}`).value;

        return {
            appPlanIntervalId: isnew === 'is-false' ? uniqueId : 0,
            appPaymentIntervalId,
            price,
        };
    });

    if (appPlanState.modalType === "update") {
        appPlanSendData.appPlanId = document.getElementById("appPlanId").value || 0;
    }

    RequestApi.fetch('/inner/appPlan/' + appPlanState.modalType, {
        method: "POST",
        body: appPlanSendData,
    })
        .then((res) => {
            if (res.success) {
                SnModal.close(appPlanState.modalName);
                SnMessage.success({ content: res.message });
                appPlanList();
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            appPlanSetLoading(false);
        });
}

function appPlanDelete(appPlanId, content = "") {
    SnModal.confirm({
        title: "¿Estás seguro de eliminar este registro?",
        content: content,
        okText: "Si",
        okType: "error",
        cancelText: "No",
        onOk() {
            appPlanSetLoading(true);
            RequestApi.fetch("/inner/appPlan/delete", {
                method: "POST",
                body: {
                    appPlanId: appPlanId || 0,
                },
            })
                .then((res) => {
                    if (res.success) {
                        SnMessage.success({ content: res.message });
                        appPlanList();
                    } else {
                        SnModal.error({ title: "Algo salió mal", content: res.message });
                    }
                })
                .finally((e) => {
                    appPlanSetLoading(false);
                });
        },
    });
}

function appPlanShowModalCreate() {
    appPlanState.modalType = "create";
    appPlanClearForm();
    SnModal.open(appPlanState.modalName);
}

function appPlanShowModalUpdate(appPlanId) {
    appPlanState.modalType = "update";
    appPlanGetById(appPlanId);
}

function appPlanGetById(appPlanId) {
    appPlanClearForm();
    appPlanSetLoading(true);

    RequestApi.fetch("/inner/appPlan/id", {
        method: "POST",
        body: {
            appPlanId: appPlanId || 0,
        },
    })
        .then((res) => {
            if (res.success) {
                document.getElementById('appPlanDescripcion').value = res.result.description;
                document.getElementById('appPlanId').value = res.result.app_plan_id;

                res.result.interval.forEach(item => addAppPlanInterval(item, false));

                SnModal.open(appPlanState.modalName);
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            appPlanSetLoading(false);
        });
}

function appPlanToExcel() {
    let dataTable = document.getElementById("appPlanCurrentTable");
    if (dataTable) {
        TableToExcel(dataTable.outerHTML, 'appPlan', 'appPlanes');
    }
}

function appPlanToPrint() {
    printArea("appPlanCurrentTable");
}

function addAppPlanInterval(item = {}, isNew = true) {
    let appPlanIntervalTableBody = document.getElementById('appPlanIntervalTableBody');
    let appPaymentIntervalHtml = '';

    if (isNew) {
        item.app_plan_interval_id = generateUniqueId();
    }

    appPaymentInterval.forEach(c => {
        appPaymentIntervalHtml += `<option value="${c.app_payment_interval_id}">${c.description}</option>`;
    });

    if (appPlanIntervalTableBody) {
        let appPlanIntervalHtml = `<tr data-isnew="${isNew ? 'is-true' : 'is-false'}" data-uniqueid="${item.app_plan_interval_id}" id="appPlanIntervalRow_${item.app_plan_interval_id}">
                            <td>
                                <div class="SnForm-item" style="margin-bottom: 0"><select class="SnForm-control" required id="appPlanIntervalProcessTypeId_${item.app_plan_interval_id}">${appPaymentIntervalHtml}</select></div>
                            </td>
                            <td>
                                <div class="SnForm-item" style="margin-bottom: 0"><input class="SnForm-control" required type="number" id="appPlanIntervalPrice_${item.app_plan_interval_id}"></div>
                            </td>
                            <td>
                                <button type="button" class="SnBtn radio icon" onclick="removeAppPlanInterval('${item.app_plan_interval_id}')"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>`;
        appPlanIntervalTableBody.insertAdjacentHTML('beforeend', appPlanIntervalHtml);

        document.getElementById(`appPlanIntervalProcessTypeId_${item.app_plan_interval_id}`).value = item.app_payment_interval_id;
        document.getElementById(`appPlanIntervalPrice_${item.app_plan_interval_id}`).value = item.price;

        appPlanSetValidate();
    }
}

function removeAppPlanInterval(appPlanIntervalId) {
    let appPlanIntervalRow = document.getElementById(`appPlanIntervalRow_${appPlanIntervalId}`);
    if (appPlanIntervalRow) {
        if (appPlanIntervalRow.dataset.isnew == 'is-true') {
            appPlanIntervalRow.remove();
            return;
        }

        SnModal.confirm({
            title: "¿Estás seguro de eliminar el registro?",
            content: '',
            okText: "Si",
            okType: "error",
            cancelText: "No",
            onOk() {
                appPlanSetLoading(true);
                RequestApi.fetch("/inner/appPlan/deleteAppPlanInterval", {
                    method: "POST",
                    body: {
                        appPlanIntervalId: appPlanIntervalId || 0,
                    },
                })
                    .then((res) => {
                        if (res.success) {
                            SnMessage.success({ content: res.message });
                            appPlanIntervalRow.remove();
                        } else {
                            SnModal.error({ title: "Algo salió mal", content: res.message });
                        }
                    })
                    .finally((e) => {
                        appPlanSetLoading(false);
                    });
            },
        });
    }
}

function appPlanSetValidate() {
    if (pValidator) {
        pValidator.destroy();
    }
    pValidator = new Pristine(document.getElementById("appPlanForm"));
}

document.addEventListener("DOMContentLoaded", () => {
    appPlanSetValidate();
    appPlanList();
});
