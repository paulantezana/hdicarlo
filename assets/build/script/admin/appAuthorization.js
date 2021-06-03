let pValidator,userRoleState={modalType:"create",modalName:"userRoleModalForm",loading:!1,currentUserRoleId:0};function userRoleList(){let e=document.getElementById("userRoleTable");e&&(SnFreeze.freeze({selector:"#userRoleTable"}),RequestApi.fetch("/admin/userRole/list").then(t=>{t.success?e.innerHTML=t.view:SnModal.error({confirm:!1,title:"Algo salió mal",content:t.message})}).finally(e=>{SnFreeze.unFreeze("#userRoleTable")}))}function userRoleLoadAuthorities(e,t){userRoleState.currentUserRoleId=e,userRoleSetLoading(!0),RequestApi.fetch("/admin/appAuthorization/byUserRoleId",{method:"POST",body:{userRoleId:e||0}}).then(o=>{if(o.success){document.querySelectorAll('#userRoleAuthList [id*="autState"]').forEach(e=>{e.checked=!1}),[...o.result].forEach(e=>{let t=document.querySelector(`#userRoleAuthList #autState${e.app_authorization_id}`);t&&(t.checked=!0)}),[...document.querySelectorAll('[id*="roleRow_"]')].forEach(e=>{e.classList.remove("active")}),document.getElementById(`roleRow_${e}`).classList.add("active"),document.getElementById("userRoleAuthSave").classList.remove("hidden"),document.getElementById("userRoleAuthTitle").textContent=t}else SnModal.error({title:"Algo salió mal",content:o.message})}).finally(e=>{userRoleSetLoading(!1)})}function userRoleSaveAuthorization(){if(!(userRoleState.currentUserRoleId>=1))return void SnModal.error({title:"Algo salió mal",content:"No se indico el rol"});let e=document.querySelectorAll("#userRoleAuthList tbody tr"),t=[];e.forEach(e=>{let o=e.dataset.id;e.querySelector(`#autState${o}`).checked&&t.push(parseInt(o))}),userRoleSetLoading(!0),RequestApi.fetch("/admin/appAuthorization/save",{method:"POST",body:{authIds:t||[],userRoleId:userRoleState.currentUserRoleId||0}}).then(e=>{e.success?SnMessage.success({content:e.message}):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{userRoleSetLoading(!1)})}function userRoleSetLoading(e){userRoleState.loading=e;let t=document.querySelectorAll(".jsUserRoleOption"),o=document.getElementById("userRoleFormSubmit");userRoleState.loading?o&&(o.setAttribute("disabled","disabled"),o.classList.add("loading"),t&&t.forEach(e=>{e.setAttribute("disabled","disabled")})):o&&(o.removeAttribute("disabled"),o.classList.remove("loading"),t&&t.forEach(e=>{e.removeAttribute("disabled")}))}function userRoleClearForm(){let e=document.getElementById("userRoleForm"),t=document.getElementById("userRoleDescription");e&&t&&(e.reset(),t.focus()),pValidator.reset()}function userRoleSubmit(){if(event.preventDefault(),!pValidator.validate())return;userRoleSetLoading(!0);let e={};e.description=document.getElementById("userRoleDescription").value,e.userRoleId=document.getElementById("userRoleFormId").value,e.state=document.getElementById("userRoleState").checked||!1,RequestApi.fetch("/admin/userRole/"+userRoleState.modalType,{method:"POST",body:e}).then(e=>{e.success?(userRoleList(),SnModal.close(userRoleState.modalName),SnMessage.success({content:e.message})):SnModal.error({confirm:!1,title:"Algo salió mal",content:e.message})}).finally(e=>{userRoleSetLoading(!1)})}function userRoleDelete(e,t=""){SnModal.confirm({title:"¿Estás seguro de eliminar este registro?",content:t,okText:"Si",okType:"error",cancelText:"No",onOk(){userRoleSetLoading(!0),RequestApi.fetch("/admin/userRole/delete",{method:"POST",body:{userRoleId:e||0}}).then(e=>{e.success?(userRoleList(),SnMessage.success({content:e.message})):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{userRoleSetLoading(!1)})}})}function userRoleShowModalCreate(){SnModal.open(userRoleState.modalName),userRoleClearForm(),userRoleState.modalType="create",document.getElementById("userRoleState").checked=!0}function userRoleShowModalUpdate(e,t){userRoleState.modalType="update",userRoleSetLoading(!0),RequestApi.fetch("/admin/userRole/id",{method:"POST",body:{userRoleId:e||0}}).then(e=>{e.success?(document.getElementById("userRoleDescription").value=e.result.description,document.getElementById("userRoleFormId").value=e.result.user_role_id,document.getElementById("userRoleState").checked="1"==e.result.state,SnModal.open(userRoleState.modalName)):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{userRoleSetLoading(!1)})}document.addEventListener("DOMContentLoaded",()=>{pValidator=new Pristine(document.getElementById("userRoleForm")),userRoleList()});