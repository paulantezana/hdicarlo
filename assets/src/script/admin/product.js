let productState = {
    modalType: "create",
    modalName: "productModalForm",
    loading: false,
};
let pValidator;

function productSetLoading(state) {
    productState.loading = state;
    let jsProductAction = document.querySelectorAll(".jsProductAction");
    let submitButton = document.getElementById("productFormSubmit");
    if (productState.loading) {
        if (submitButton) {
            submitButton.setAttribute("disabled", "disabled");
            submitButton.classList.add("loading");
        }
        if (jsProductAction) {
            jsProductAction.forEach((item) => {
                item.setAttribute("disabled", "disabled");
            });
        }
    } else {
        if (submitButton) {
            submitButton.removeAttribute("disabled");
            submitButton.classList.remove("loading");
        }
        if (jsProductAction) {
            jsProductAction.forEach((item) => {
                item.removeAttribute("disabled");
            });
        }
    }
}

function productList(page = 1, limit = 20, search = "") {
    let productTable = document.getElementById("productTable");
    if (productTable) {
        SnFreeze.freeze({ selector: "#productTable" });
        RequestApi.fetch(
            `/admin/product/table?limit=${limit}&page=${page}&search=${search}`,
            {
                method: "GET",
            }
        )
            .then((res) => {
                if (res.success) {
                    productTable.innerHTML = res.view;
                } else {
                    SnModal.error({ title: "Algo salió mal", content: res.message });
                }
            })
            .finally((e) => {
                SnFreeze.unFreeze("#productTable");
            });
    }
}

function productClearForm() {
    let currentForm = document.getElementById("productForm");
    let productBarCode = document.getElementById("productBarCode");
    if (currentForm) {
        currentForm.reset();
    }
    if (productBarCode) {
        setTimeout(() => {
            productBarCode.focus();
        }, 500)
    }
    pValidator.reset();
}

function productSubmit(e) {
    e.preventDefault();
    if (!pValidator.validate()) {
        return;
    }
    productSetLoading(true);

    let productSendData = {};
    productSendData.title = document.getElementById("productTitle").value;
    productSendData.barCode = document.getElementById("productBarCode").value;
    productSendData.price = document.getElementById("productPrice").value;

    if (productState.modalType === "update") {
        productSendData.productId = document.getElementById("productId").value || 0;
    }

    RequestApi.fetch('/admin/product/' + productState.modalType, {
        method: "POST",
        body: productSendData,
    })
        .then((res) => {
            if (res.success) {
                SnModal.close(productState.modalName);
                SnMessage.success({ content: res.message });
                productList();
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            productSetLoading(false);
        });
}

function productDelete(productId, content = "") {
    SnModal.confirm({
        title: "¿Estás seguro de eliminar este registro?",
        content: content,
        okText: "Si",
        okType: "error",
        cancelText: "No",
        onOk() {
            productSetLoading(true);
            RequestApi.fetch("/admin/product/delete", {
                method: "POST",
                body: {
                    productId: productId || 0,
                },
            })
                .then((res) => {
                    if (res.success) {
                        SnMessage.success({ content: res.message });
                        productList();
                    } else {
                        SnModal.error({ title: "Algo salió mal", content: res.message });
                    }
                })
                .finally((e) => {
                    productSetLoading(false);
                });
        },
    });
}

function productShowModalCreate() {
    productState.modalType = "create";
    productClearForm();
    SnModal.open(productState.modalName);
}

function productShowModalUpdate(productId) {
    productState.modalType = "update";
    productGetById(productId);
}

function productGetById(productId) {
    productClearForm();
    productSetLoading(true);

    RequestApi.fetch("/admin/product/id", {
        method: "POST",
        body: {
            productId: productId || 0,
        },
    })
        .then((res) => {
            if (res.success) {
                document.getElementById('productTitle').value = res.result.title;
                document.getElementById('productBarCode').value = res.result.bar_code;
                document.getElementById('productPrice').value = res.result.price;
                document.getElementById('productId').value = res.result.product_id;

                SnModal.open(productState.modalName);
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            productSetLoading(false);
        });
}

function productToExcel() {
    let dataTable = document.getElementById("productCurrentTable");
    if (dataTable) {
        TableToExcel(dataTable.outerHTML, 'Product', 'Productes');
    }
}

function productToPrint() {
    printArea("productCurrentTable");
}

document.addEventListener("DOMContentLoaded", () => {
    pValidator = new Pristine(document.getElementById("productForm"));

    document.getElementById("searchContent").addEventListener("input", (e) => {
        productList(1, 10, e.target.value);
    });

    productList();
});
