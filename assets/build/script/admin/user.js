let pValidator,userState={modalType:"create",modalName:"userModalForm",loading:!1};function userSetLoading(e){userState.loading=e;let t=document.querySelectorAll(".jsUserAction"),n=document.getElementById("userFormSubmit");userState.loading?(n&&(n.setAttribute("disabled","disabled"),n.classList.add("loading")),t&&t.forEach(e=>{e.setAttribute("disabled","disabled")})):(n&&(n.removeAttribute("disabled"),n.classList.remove("loading")),t&&t.forEach(e=>{e.removeAttribute("disabled")}))}function userList(e=1,t=20,n=""){let r=document.getElementById("userTable");r&&(SnFreeze.freeze({selector:"#userTable"}),RequestApi.fetch(`/admin/user/table?limit=${t}&page=${e}&search=${n}`,{method:"GET"}).then(e=>{e.success?r.innerHTML=e.view:SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{SnFreeze.unFreeze("#userTable")}))}function userClearForm(){let e=document.getElementById("userForm"),t=document.getElementById("userEmail");e&&t&&(e.reset(),t.focus()),pValidator.reset()}function userSubmit(e){if(e.preventDefault(),!pValidator.validate())return;userSetLoading(!0);let t={};t.password=document.getElementById("userPassword").value,t.passwordConfirm=document.getElementById("userPasswordConfirm").value,t.email=document.getElementById("userEmail").value,t.userName=document.getElementById("userUserName").value,t.fullName=document.getElementById("userFullName").value,t.identityDocumentId=document.getElementById("userIdentityDocumentId").value,t.identityDocumentNumber=document.getElementById("userIdentityDocumentNumber").value,t.lastName=document.getElementById("userLastName").value,t.state=document.getElementById("userState").checked||!1,t.userRoleId=document.getElementById("userUserRoleId").value,"update"===userState.modalType&&(t.userId=document.getElementById("userId").value||0),"updatePassword"===userState.modalType&&(t={password:document.getElementById("userPassword").value,passwordConfirm:document.getElementById("userPasswordConfirm").value,userId:document.getElementById("userId").value||0}),RequestApi.fetch("/admin/user/"+userState.modalType,{method:"POST",body:t}).then(e=>{e.success?(SnModal.close(userState.modalName),SnMessage.success({content:e.message}),userList()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{userSetLoading(!1)})}function userDelete(e,t=""){SnModal.confirm({title:"¿Estás seguro de eliminar este registro?",content:t,okText:"Si",okType:"error",cancelText:"No",onOk(){userSetLoading(!0),RequestApi.fetch("/admin/user/delete",{method:"POST",body:{userId:e||0}}).then(e=>{e.success?(SnMessage.success({content:e.message}),userList()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{userSetLoading(!1)})}})}function userShowModalCreate(){userState.modalType="create",userClearForm(),prepareModalUser(userState.modalType),SnModal.open(userState.modalName)}function userShowModalUpdatePassword(e){userState.modalType="updatePassword",prepareModalUser(userState.modalType),userGetById(e)}function userShowModalUpdate(e){userState.modalType="update",prepareModalUser(userState.modalType),userGetById(e)}function prepareModalUser(e=""){pValidator.destroy(),document.getElementById("userEmail").parentElement.parentElement.classList.remove("hidden"),document.getElementById("userUserName").parentElement.parentElement.classList.remove("hidden"),document.getElementById("userIdentityDocumentId").parentElement.classList.remove("hidden"),document.getElementById("userIdentityDocumentNumber").parentElement.parentElement.parentElement.classList.remove("hidden"),document.getElementById("userLastName").parentElement.parentElement.classList.remove("hidden"),document.getElementById("userFullName").parentElement.parentElement.classList.remove("hidden"),document.getElementById("userState").parentElement.parentElement.classList.remove("hidden"),document.getElementById("userUserRoleId").parentElement.classList.remove("hidden"),document.getElementById("userPassword").parentElement.parentElement.classList.remove("hidden"),document.getElementById("userPasswordConfirm").parentElement.parentElement.classList.remove("hidden"),document.getElementById("userEmail").removeAttribute("required"),document.getElementById("userUserName").removeAttribute("required"),document.getElementById("userIdentityDocumentId").removeAttribute("required"),document.getElementById("userIdentityDocumentNumber").removeAttribute("required"),document.getElementById("userLastName").removeAttribute("required"),document.getElementById("userFullName").removeAttribute("required"),document.getElementById("userState").removeAttribute("required"),document.getElementById("userUserRoleId").removeAttribute("required"),document.getElementById("userPassword").removeAttribute("required"),document.getElementById("userPasswordConfirm").removeAttribute("required"),"update"===e||"create"===e?(document.getElementById("userEmail").setAttribute("required",!0),document.getElementById("userUserName").setAttribute("required",!0),document.getElementById("userIdentityDocumentId").setAttribute("required",!0),document.getElementById("userIdentityDocumentNumber").setAttribute("required",!0),document.getElementById("userLastName").setAttribute("required",!0),document.getElementById("userFullName").setAttribute("required",!0),document.getElementById("userUserRoleId").setAttribute("required",!0),"update"===e&&(document.getElementById("userPassword").parentElement.parentElement.classList.add("hidden"),document.getElementById("userPasswordConfirm").parentElement.parentElement.classList.add("hidden")),"create"===e&&(document.getElementById("userPassword").setAttribute("required",!0),document.getElementById("userPasswordConfirm").setAttribute("required",!0),document.getElementById("userState").checked=!0)):"updatePassword"===e&&(document.getElementById("userEmail").parentElement.parentElement.classList.add("hidden"),document.getElementById("userUserName").parentElement.parentElement.classList.add("hidden"),document.getElementById("userIdentityDocumentId").parentElement.classList.add("hidden"),document.getElementById("userIdentityDocumentNumber").parentElement.parentElement.parentElement.classList.add("hidden"),document.getElementById("userLastName").parentElement.parentElement.classList.add("hidden"),document.getElementById("userFullName").parentElement.parentElement.classList.add("hidden"),document.getElementById("userState").parentElement.parentElement.classList.add("hidden"),document.getElementById("userUserRoleId").parentElement.classList.add("hidden"),document.getElementById("userPassword").setAttribute("required",!0),document.getElementById("userPasswordConfirm").setAttribute("required",!0)),pValidator=new Pristine(document.getElementById("userForm"))}function userGetById(e){userClearForm(),userSetLoading(!0),RequestApi.fetch("/admin/user/id",{method:"POST",body:{userId:e||0}}).then(e=>{e.success?(document.getElementById("userEmail").value=e.result.email,document.getElementById("userUserName").value=e.result.user_name,document.getElementById("userFullName").value=e.result.full_name,document.getElementById("userIdentityDocumentId").value=e.result.identity_document_id,document.getElementById("userIdentityDocumentNumber").value=e.result.identity_document_number,document.getElementById("userLastName").value=e.result.last_name,document.getElementById("userState").checked="0"!=e.result.state,document.getElementById("userUserRoleId").value=e.result.user_role_id,document.getElementById("userId").value=e.result.user_id,SnModal.open(userState.modalName)):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{userSetLoading(!1)})}function userToExcel(){let e=document.getElementById("userCurrentTable");e&&TableToExcel(e.outerHTML,"Usuario","Usuario")}function getIdentityDocumentNumber(){let e=document.getElementById("userIdentityDocumentNumber"),t=document.getElementById("userIdentityDocumentId"),n=document.getElementById("userSearchIdentityDocumentNumber");n.classList.add("loading"),RequestApi.fetch("/page/queryDocument",{method:"POST",body:{documentNumber:e.value,documentTypeId:t.value}}).then(e=>{e.success?(document.getElementById("userFullName").value=e.result.full_name,document.getElementById("userLastName").value=`${e.result.father_last__name} ${e.result.mother_last_name}`):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{n.classList.remove("loading")})}document.addEventListener("DOMContentLoaded",()=>{pValidator=new Pristine(document.getElementById("userForm")),document.getElementById("searchContent").addEventListener("keyup",e=>{"Enter"===e.key&&userList(1,10,e.target.value)}),userList()});