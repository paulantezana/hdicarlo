let companyPaymentPValidator;
document.addEventListener("DOMContentLoaded", () => {
    companyPaymentPValidator = new Pristine(document.getElementById("companyPaymentForm"));
    moment.locale('es');
    companyPaymentAll();
});

function companyPaymentAll() {
    let companyPaymentTable = document.getElementById("companyPaymentTable");
    if (companyPaymentTable) {
        SnFreeze.freeze({ selector: "#companyPaymentTable" });
        RequestApi.fetch('/inner/company/getAllPayment', {
            method: "POST",
            body: { companyId },
        }
        )
            .then((res) => {
                if (res.success) {
                    companyPaymentTable.innerHTML = res.view;
                } else {
                    SnModal.error({ title: "Algo salió mal", content: res.message });
                }
            })
            .finally((e) => {
                SnFreeze.unFreeze("#companyPaymentTable");
            });
    }
}

function companyPaymentSetLoading(state) {
    let loaders = document.querySelectorAll(".jsCompanyPaymentAction");
    let submitBtn = document.getElementById("companyPaymentFormSubmit");
    if (state) {
        if (submitBtn) {
            submitBtn.setAttribute("disabled", "disabled");
            submitBtn.classList.add("loading");
        }
        if (loaders) {
            loaders.forEach((item) => {
                item.setAttribute("disabled", "disabled");
            });
        }
    } else {
        if (submitBtn) {
            submitBtn.removeAttribute("disabled");
            submitBtn.classList.remove("loading");
        }
        if (loaders) {
            loaders.forEach((item) => {
                item.removeAttribute("disabled");
            });
        }
    }
}

function companyPaymentShowModalCreate() {
    companyPaymentSetLoading(true);
    RequestApi.fetch("/inner/company/getLastPayment", {
        method: "POST",
        body: { companyId },
    })
        .then((res) => {
            if (res.success) {
                companyPaymentClearForm();
                SnModal.open('companyPaymentModalForm');

                let lastPayment = res.result.lastPayment;
                let company = res.result.company;

                let paymentFromDatetime = lastPayment === false
                    ? company.contract_date_of_issue
                    : (
                        moment(company.contract_date_of_issue).diff(moment(lastPayment.to_date_time)) >= 0
                            ? company.contract_date_of_issue
                            : lastPayment.to_date_time
                    );

                let MfromDateTome = moment(paymentFromDatetime);
                document.getElementById('companyPaymentFromDatetime').value = MfromDateTome.format('YYYY-MM-DD');
                document.getElementById('companyPaymentPrice').value = company.app_plan_price;
                document.getElementById('companyPaymentDescription').innerHTML = company.app_plan_description;


                let Mdate = moment(paymentFromDatetime);
                Mdate.add(1, company.app_plan_date_interval);
                document.getElementById('companyPaymentToDatetime').value = Mdate.format('YYYY-MM-DD');
                document.getElementById('companyPaymentTotal').value = company.app_plan_price;

            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            companyPaymentSetLoading(false);
        });
}

function companyPaymentClearForm() {
    let currentForm = document.getElementById("companyPaymentForm");
    let paymentReference = document.getElementById("companyPaymentReference");
    if (currentForm && paymentReference) {
        currentForm.reset();
        paymentReference.focus();
    }
    companyPaymentPValidator.reset();
}

function companyPaymentSubmit(e) {
    e.preventDefault();
    if (!companyPaymentPValidator.validate()) {
        return;
    }
    companyPaymentSetLoading(true);

    let paymentSendData = {};
    paymentSendData.reference = document.getElementById("companyPaymentReference").value;
    paymentSendData.fromDatetime = document.getElementById("companyPaymentFromDatetime").value;
    paymentSendData.toDatetime = document.getElementById("companyPaymentToDatetime").value;
    paymentSendData.description = document.getElementById("companyPaymentDescription").value;
    paymentSendData.total = document.getElementById("companyPaymentTotal").value;
    paymentSendData.companyId = companyId;

    RequestApi.fetch('/inner/company/createPayment', {
        method: "POST",
        body: paymentSendData,
    })
        .then((res) => {
            if (res.success) {
                SnModal.close('companyPaymentModalForm');
                SnMessage.success({ content: res.message });
                companyPaymentAll();
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            companyPaymentSetLoading(false);
        });
}

function companyPaymentCanceled(appPaymentId, content){
    SnModal.confirm({
        title: `¿Estás seguro de anular este registro con código: ${content}?`,
        content: 'Ingrese el motivo de la anulación',
        input: true,
        okText: "Si",
        okType: "error",
        cancelText: "No",
        onOk(message) {
        companyPaymentSetLoading(true);
          RequestApi.fetch("/inner/company/canceledPayment", {
            method: "POST",
            body: {
                appPaymentId,
                companyId,
                message,
            },
          })
            .then((res) => {
              if (res.success) {
                SnMessage.success({ content: res.message });
                companyPaymentAll();
              } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
              }
            })
            .finally((e) => {
              companyPaymentSetLoading(false);
            });
        },
      });
}

function companyPaymentToPrint() {
    // printArea("paymentCurrentTable");
}

function companyPaymentToExcel() {
    // let dataTable = document.getElementById("paymentCurrentTable");
    // if (dataTable) {
    //     TableToExcel(dataTable.outerHTML, 'Payment', 'Paymentes');
    // }
}