function queryRucSubmit(){let e={};e.ruc=document.getElementById("ruc").value,e.googleKey=document.getElementById("googleKey").value,RequestApi.fetch("/page/rucQuery/",{method:"POST",body:e}).then(e=>{document.getElementById("queryRucResult").innerHTML="",e.success?(SnMessage.success({content:e.message}),document.getElementById("queryRucResult").innerHTML=e.view,document.getElementById("queryRuc").style.display="none"):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{})}function queryRucSubmitNewQuery(){location.reload()}