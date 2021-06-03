let companyStateModalType = 'create';
let pValidator;

document.addEventListener("DOMContentLoaded", () => {
    pValidator = new Pristine(document.getElementById("companyForm"));

    document.getElementById("searchContent").addEventListener("keyup", (e) => {
        if (e.key === 'Enter') {
            companyList(1, 10, e.target.value);
        }
    });

    companyList();
});

function companySetLoading(state) {
    let jsCompanyAction = document.querySelectorAll(".jsCompanyAction");
    let submitButton = document.getElementById("companyFormSubmit");
    if (state) {
        if (submitButton) {
            submitButton.setAttribute("disabled", "disabled");
            submitButton.classList.add("loading");
        }
        if (jsCompanyAction) {
            jsCompanyAction.forEach((item) => {
                item.setAttribute("disabled", "disabled");
            });
        }
    } else {
        if (submitButton) {
            submitButton.removeAttribute("disabled");
            submitButton.classList.remove("loading");
        }
        if (jsCompanyAction) {
            jsCompanyAction.forEach((item) => {
                item.removeAttribute("disabled");
            });
        }
    }
}

function companyList(page = 1, limit = 20, search = "") {
    let companyTable = document.getElementById("companyTable");
    if (companyTable) {
        SnFreeze.freeze({ selector: "#companyTable" });
        RequestApi.fetch(
            `/inner/company/table?limit=${limit}&page=${page}&search=${search}`,
            {
                method: "GET",
            }
        )
            .then((res) => {
                if (res.success) {
                    companyTable.innerHTML = res.view;
                } else {
                    SnModal.error({ title: "Algo salió mal", content: res.message });
                }
            })
            .finally((e) => {
                SnFreeze.unFreeze("#companyTable");
            });
    }
}

function companyClearForm() {
    let currentForm = document.getElementById("companyForm");
    let companyDocumentNumber = document.getElementById("companyDocumentNumber");
    if (currentForm && companyDocumentNumber) {
        currentForm.reset();
        setTimeout(() => {
            companyDocumentNumber.focus();
        }, 500);
    }
    pValidator.reset();
}

function prepareModalUser(mode = "") {
    pValidator.destroy();

    document.getElementById("userPassword").parentElement.parentElement.classList.remove("hidden");
    document.getElementById("userPasswordConfirm").parentElement.parentElement.classList.remove("hidden");
    // document.getElementById("companyAppPlanId").parentElement.classList.remove("hidden");
    // document.getElementById("companyContractDateNumber").parentElement.parentElement.classList.remove("hidden");
    // document.getElementById("companyContractDateFactor").parentElement.parentElement.classList.remove("hidden");
    document.getElementById("userPassword").removeAttribute("required");
    document.getElementById("userPasswordConfirm").removeAttribute("required");
    // document.getElementById("companyAppPlanId").removeAttribute("required");
    // document.getElementById("companyContractDateNumber").removeAttribute("required");
    // document.getElementById("companyContractDateFactor").removeAttribute("required");

    if (mode === "update") {
        document.getElementById("userPassword").parentElement.parentElement.classList.add("hidden");
        document.getElementById("userPasswordConfirm").parentElement.parentElement.classList.add("hidden");
        // document.getElementById("companyAppPlanId").parentElement.classList.add("hidden");
        // document.getElementById("companyContractDateNumber").parentElement.parentElement.classList.add("hidden");
        // document.getElementById("companyContractDateFactor").parentElement.parentElement.classList.add("hidden");
    }

    pValidator = new Pristine(document.getElementById("companyForm"));
}

function companySubmit(e) {
    e.preventDefault();
    if (!pValidator.validate()) {
        return;
    }
    companySetLoading(true);

    let companySendData = {};
    companySendData.documentNumber = document.getElementById("companyDocumentNumber").value;
    companySendData.socialReason = document.getElementById("companySocialReason").value;
    companySendData.commercialReason = document.getElementById("companyCommercialReason").value;
    companySendData.fiscalAddress = document.getElementById("companyFiscalAddress").value;
    companySendData.email = document.getElementById("companyEmail").value;
    companySendData.phone = document.getElementById("companyPhone").value;
    companySendData.telephone = document.getElementById("companyTelephone").value;
    companySendData.urlWeb = document.getElementById("companyUrlWeb").value;
    companySendData.representative = document.getElementById("companyRepresentative").value;
    companySendData.appPlanId = document.getElementById("companyAppPlanId").value;
    companySendData.appPaymentIntervalId = document.getElementById("companyAppPaymentIntervalId").value;
    companySendData.userPassword = document.getElementById("userPassword").value;
    companySendData.userPasswordConfirm = document.getElementById("userPasswordConfirm").value;

    if (companyStateModalType === "update") {
        companySendData.companyId = document.getElementById("companyId").value || 0;
    }

    RequestApi.fetch('/inner/company/' + companyStateModalType, {
        method: "POST",
        body: companySendData,
    })
        .then((res) => {
            if (res.success) {
                SnModal.close('companyModalForm');
                SnMessage.success({ content: res.message });
                companyList();
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            companySetLoading(false);
        });
}

function companyDelete(companyId, content = "") {
    SnModal.confirm({
        title: "¿Estás seguro de eliminar este registro?",
        content: content,
        okText: "Si",
        okType: "error",
        cancelText: "No",
        onOk() {
            companySetLoading(true);
            RequestApi.fetch("/inner/company/delete", {
                method: "POST",
                body: {
                    companyId: companyId || 0,
                },
            })
                .then((res) => {
                    if (res.success) {
                        SnMessage.success({ content: res.message });
                        companyList();
                    } else {
                        SnModal.error({ title: "Algo salió mal", content: res.message });
                    }
                })
                .finally((e) => {
                    companySetLoading(false);
                });
        },
    });
}

function companyChangeDevelopment(companyId, development, content = "") {
    SnModal.confirm({
        title: `¿Estás seguro de pasar a modo ${development == 0 ? 'Producción' : 'Desarrollo'}?`,
        content: content,
        okText: "Si",
        okType: "error",
        cancelText: "No",
        onOk() {
            companySetLoading(true);
            RequestApi.fetch("/inner/company/changeDevelopment", {
                method: "POST",
                body: {
                    companyId: companyId || 0,
                    development: !(development == 1),
                },
            })
                .then((res) => {
                    if (res.success) {
                        SnMessage.success({ content: res.message });
                        companyList();
                    } else {
                        SnModal.error({ title: "Algo salió mal", content: res.message });
                    }
                })
                .finally((e) => {
                    companySetLoading(false);
                });
        },
    });
}

function companyShowModalCreate() {
    companyStateModalType = "create";
    companyClearForm();
    prepareModalUser(companyStateModalType);
    SnModal.open('companyModalForm');
}

function companyShowModalUpdate(companyId) {
    companyStateModalType = "update";
    companyGetById(companyId);
}

function companyGetById(companyId) {
    companyClearForm();
    prepareModalUser(companyStateModalType);
    companySetLoading(true);

    RequestApi.fetch("/inner/company/id", {
        method: "POST",
        body: {
            companyId: companyId || 0,
        },
    })
        .then((res) => {
            if (res.success) {
                document.getElementById('companyDocumentNumber').value = res.result.document_number;
                document.getElementById('companySocialReason').value = res.result.social_reason;
                document.getElementById('companyCommercialReason').value = res.result.commercial_reason;
                document.getElementById('companyFiscalAddress').value = res.result.fiscal_address;
                document.getElementById('companyEmail').value = res.result.email;
                document.getElementById('companyPhone').value = res.result.phone;
                document.getElementById('companyTelephone').value = res.result.telephone;
                document.getElementById('companyUrlWeb').value = res.result.url_web;
                document.getElementById('companyRepresentative').value = res.result.representative;
                document.getElementById('companyAppPlanId').value = res.result.app_plan_id;
                document.getElementById('companyAppPaymentIntervalId').value = res.result.app_payment_interval_id;

                document.getElementById('companyId').value = res.result.company_id;

                SnModal.open('companyModalForm');
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            companySetLoading(false);
        });
}

function searchDocumentChangeState(state) {
    let companySearchDocument = document.getElementById('companySearchDocument');
    if (companySearchDocument) {
        if (state) {
            companySearchDocument.setAttribute("disabled", "disabled");
        } else {
            companySearchDocument.removeAttribute("disabled");
        }
    }
}

function companySearchDocument() {
    let searchDocumentNumber = document.getElementById('companyDocumentNumber').value

    searchDocumentChangeState(true);
    RequestApi.fetch('/page/queryDocument', {
        method: 'POST',
        body: {
            documentNumber: searchDocumentNumber,
            documentTypeId: 3,
        }
    })
        .then(res => {
            if (res.success) {
                document.getElementById('companySocialReason').value = res.result.full_name;
                document.getElementById('companyFiscalAddress').value = res.result.full_address;
            } else {
                SnModal.error({
                    title: "Algo salió mal",
                    content: res.message
                });
            }
        })
        .finally(e => {
            searchDocumentChangeState(false);
        });
}

function companyToExcel() {
    let dataTable = document.getElementById("companyCurrentTable");
    if (dataTable) {
        TableToExcel(dataTable.outerHTML, 'Company', 'Companyes');
    }
}

function companyShowLogoModal(companyId) {
    companySetLoading(true);
    RequestApi.fetch("/inner/company/id", {
        method: "POST",
        body: {
            companyId: companyId || 0,
        },
    })
        .then((res) => {
            if (res.success) {
                document.getElementById('companyLogoId').value = res.result.company_id;
                document.getElementById('companyLogoSquareImg').setAttribute('src', URL_PATH + res.result.logo);
                document.getElementById('companyLogoLargeImg').setAttribute('src', URL_PATH + res.result.logo_large);

                SnModal.open('companyLogoModal');
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            companySetLoading(false);
        });
}

function uploadLogoSquare() {
    let element = document.getElementById('companyLogoSquare');
    if (element == null) {
        return;
    }

    if (element.files === undefined) {
        SnModal.error({ title: "Error de usuario", content: 'Elije almenos un archivo' });
        return;
    }

    let archivo = element.files[0];

    if (archivo == undefined || archivo == null) {
        SnModal.error({ title: "Error de usuario", content: 'Elije almenos un archivo' });
        return;
    }

    if (validateFile(archivo, ['image/png', 'image/jpeg', 'image/jpg'], 100)) {
        SnModal.confirm({
            title: "¿Estás seguro de subir el logo?",
            content: 'Logo cuadrada de la empresa',
            okText: "Si",
            okType: "error",
            cancelText: "No",
            onOk() {
                let data = new FormData();
                data.append('logo', archivo);
                data.append('companyId', document.getElementById("companyLogoId").value);

                SnFreeze.freeze({ selector: '#companyLogoSquareWrapper' });
                RequestApi.fetch("/inner/company/uploadLogoSquare", {
                    method: "POST",
                    body: data,
                })
                    .then((res) => {
                        if (res.success) {
                            SnMessage.success({ content: res.message });
                            SnModal.close('companyLogoModal');
                            companyList();
                        } else {
                            SnModal.error({ title: "Algo salió mal", content: res.message });
                        }
                    })
                    .finally((e) => {
                        SnFreeze.unFreeze('#companyLogoSquareWrapper');
                    });
            },
        });
    } else {
        SnModal.error({ title: "Error de usuario", content: 'El archivo tiene formato o tamaño incorrecto, solo se aceptan archivos con extension [image/png,image/jpeg,image/jpg]. y un tamaño maximo de 100Kb.' });
    }
}

function uploadLogoLarge() {
    let element = document.getElementById('companyLogoLarge');
    if (element == null) {
        return;
    }

    if (element.files === undefined) {
        SnModal.error({ title: "Error de usuario", content: 'Elije almenos un archivo' });
        return;
    }

    let archivo = element.files[0];

    if (archivo == undefined || archivo == null) {
        SnModal.error({ title: "Error de usuario", content: 'Elije almenos un archivo' });
        return;
    }

    if (validateFile(archivo, ['image/png', 'image/jpeg', 'image/jpg'], 100)) {
        SnModal.confirm({
            title: "¿Estás seguro de subir el logo?",
            content: 'Logo largo de la empresa',
            okText: "Si",
            okType: "error",
            cancelText: "No",
            onOk() {
                let data = new FormData();
                data.append('logo', archivo);
                data.append('companyId', document.getElementById("companyLogoId").value);

                SnFreeze.freeze({ selector: '#companyLogoLargeWrapper' });
                RequestApi.fetch("/inner/company/uploadLogoLarge", {
                    method: "POST",
                    body: data,
                })
                    .then((res) => {
                        if (res.success) {
                            SnMessage.success({ content: res.message });
                            SnModal.close('companyLogoModal');
                            companyList();
                        } else {
                            SnModal.error({ title: "Algo salió mal", content: res.message });
                        }
                    })
                    .finally((e) => {
                        SnFreeze.unFreeze('#companyLogoLargeWrapper');
                    });
            },
        });
    } else {
        SnModal.error({ title: "Error de usuario", content: 'El archivo tiene formato o tamaño incorrecto, solo se aceptan archivos con extension [image/png,image/jpeg,image/jpg]. y un tamaño maximo de 100Kb.' });
    }
}

function companyToPrint() {
    printArea("companyCurrentTable");
}
