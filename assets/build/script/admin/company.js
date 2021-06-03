let pValidator;function companySetLoading(e){let o=document.querySelectorAll(".jsCompanyAction"),a=document.getElementById("companyFormSubmit");e?(a&&(a.setAttribute("disabled","disabled"),a.classList.add("loading")),o&&o.forEach(e=>{e.setAttribute("disabled","disabled")})):(a&&(a.removeAttribute("disabled"),a.classList.remove("loading")),o&&o.forEach(e=>{e.removeAttribute("disabled")}))}function companySubmit(e){if(e.preventDefault(),!pValidator.validate())return;companySetLoading(!0);let o={};o.documentNumber=document.getElementById("companyDocumentNumber").value,o.socialReason=document.getElementById("companySocialReason").value,o.commercialReason=document.getElementById("companyCommercialReason").value,o.fiscalAddress=document.getElementById("companyFiscalAddress").value,o.email=document.getElementById("companyEmail").value,o.phone=document.getElementById("companyPhone").value,o.representative=document.getElementById("companyRepresentative").value,o.telephone=document.getElementById("companyTelephone").value,o.urlWeb=document.getElementById("companyUrlWeb").value,o.companyId=document.getElementById("companyId").value||0,RequestApi.fetch("/admin/company/update",{method:"POST",body:o}).then(e=>{e.success?(SnMessage.success({content:e.message}),location.reload()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{companySetLoading(!1)})}function uploadLogoSquare(){let e=document.getElementById("companyLogoSquare");if(null==e)return;if(void 0===e.files)return void SnModal.error({title:"Error de usuario",content:"Elije almenos un archivo"});let o=e.files[0];null!=o&&null!=o?validateFile(o,["image/png","image/jpeg","image/jpg"],100)?SnModal.confirm({title:"¿Estás seguro de subir el logo?",content:"Logo cuadrada de la empresa",okText:"Si",okType:"error",cancelText:"No",onOk(){let e=new FormData;e.append("logo",o),e.append("companyId",document.getElementById("companyId").value),SnFreeze.freeze({selector:"#companyLogoSquareWrapper"}),RequestApi.fetch("/admin/company/uploadLogoSquare",{method:"POST",body:e}).then(e=>{e.success?(SnMessage.success({content:e.message}),location.reload()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{SnFreeze.unFreeze("#companyLogoSquareWrapper")})}}):SnModal.error({title:"Error de usuario",content:"El archivo tiene formato o tamaño incorrecto, solo se aceptan archivos con extension [image/png,image/jpeg,image/jpg]. y un tamaño maximo de 100Kb."}):SnModal.error({title:"Error de usuario",content:"Elije almenos un archivo"})}function uploadLogoLarge(){let e=document.getElementById("companyLogoLarge");if(null==e)return;if(void 0===e.files)return void SnModal.error({title:"Error de usuario",content:"Elije almenos un archivo"});let o=e.files[0];null!=o&&null!=o?validateFile(o,["image/png","image/jpeg","image/jpg"],100)?SnModal.confirm({title:"¿Estás seguro de subir el logo?",content:"Logo largo de la empresa",okText:"Si",okType:"error",cancelText:"No",onOk(){let e=new FormData;e.append("logo",o),e.append("companyId",document.getElementById("companyId").value),SnFreeze.freeze({selector:"#companyLogoLargeWrapper"}),RequestApi.fetch("/admin/company/uploadLogoLarge",{method:"POST",body:e}).then(e=>{e.success?(SnMessage.success({content:e.message}),location.reload()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{SnFreeze.unFreeze("#companyLogoLargeWrapper")})}}):SnModal.error({title:"Error de usuario",content:"El archivo tiene formato o tamaño incorrecto, solo se aceptan archivos con extension [image/png,image/jpeg,image/jpg]. y un tamaño maximo de 100Kb."}):SnModal.error({title:"Error de usuario",content:"Elije almenos un archivo"})}document.addEventListener("DOMContentLoaded",()=>{pValidator=new Pristine(document.getElementById("companyForm"))});