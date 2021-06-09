let pValidator,productState={modalType:"create",modalName:"productModalForm",loading:!1};function productSetLoading(e){productState.loading=e;let t=document.querySelectorAll(".jsProductAction"),o=document.getElementById("productFormSubmit");productState.loading?(o&&(o.setAttribute("disabled","disabled"),o.classList.add("loading")),t&&t.forEach(e=>{e.setAttribute("disabled","disabled")})):(o&&(o.removeAttribute("disabled"),o.classList.remove("loading")),t&&t.forEach(e=>{e.removeAttribute("disabled")}))}function productList(e=1,t=20,o=""){let d=document.getElementById("productTable");d&&(SnFreeze.freeze({selector:"#productTable"}),RequestApi.fetch(`/admin/product/table?limit=${t}&page=${e}&search=${o}`,{method:"GET"}).then(e=>{e.success?d.innerHTML=e.view:SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{SnFreeze.unFreeze("#productTable")}))}function productClearForm(){let e=document.getElementById("productForm"),t=document.getElementById("productBarCode");e&&e.reset(),t&&setTimeout(()=>{t.focus()},500),pValidator.reset()}function productSubmit(e){if(e.preventDefault(),!pValidator.validate())return;productSetLoading(!0);let t={};t.title=document.getElementById("productTitle").value,t.barCode=document.getElementById("productBarCode").value,t.price=document.getElementById("productPrice").value,"update"===productState.modalType&&(t.productId=document.getElementById("productId").value||0),RequestApi.fetch("/admin/product/"+productState.modalType,{method:"POST",body:t}).then(e=>{e.success?(SnModal.close(productState.modalName),SnMessage.success({content:e.message}),productList()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{productSetLoading(!1)})}function productDelete(e,t=""){SnModal.confirm({title:"¿Estás seguro de eliminar este registro?",content:t,okText:"Si",okType:"error",cancelText:"No",onOk(){productSetLoading(!0),RequestApi.fetch("/admin/product/delete",{method:"POST",body:{productId:e||0}}).then(e=>{e.success?(SnMessage.success({content:e.message}),productList()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{productSetLoading(!1)})}})}function productShowModalCreate(){productState.modalType="create",productClearForm(),SnModal.open(productState.modalName)}function productShowModalUpdate(e){productState.modalType="update",productGetById(e)}function productGetById(e){productClearForm(),productSetLoading(!0),RequestApi.fetch("/admin/product/id",{method:"POST",body:{productId:e||0}}).then(e=>{e.success?(document.getElementById("productTitle").value=e.result.title,document.getElementById("productBarCode").value=e.result.bar_code,document.getElementById("productPrice").value=e.result.price,document.getElementById("productId").value=e.result.product_id,SnModal.open(productState.modalName)):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{productSetLoading(!1)})}function productToExcel(){let e=document.getElementById("productCurrentTable");e&&TableToExcel(e.outerHTML,"Product","Productes")}function productToPrint(){printArea("productCurrentTable")}document.addEventListener("DOMContentLoaded",()=>{pValidator=new Pristine(document.getElementById("productForm")),document.getElementById("searchContent").addEventListener("input",e=>{productList(1,10,e.target.value)}),productList()});