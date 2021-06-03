document.addEventListener("DOMContentLoaded",()=>{reportIncomeList()});let chartColors=["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)"];function reportIncomeList(){let e=document.getElementById("reportIncomeTable"),r=document.getElementById("currentYear");if(r){if(!(r.value>1900&&r.value<3e3))return void SnMessage.success({content:"Error usuario: Ingrese un año válido"});e&&(SnFreeze.freeze({selector:"#reportIncomeTable"}),RequestApi.fetch(`/inner/report/incomeTable?year=${r.value}`,{method:"GET"}).then(r=>{r.success?(e.innerHTML=r.view,buildIcomeChart(r.result.paymentIncome),buildMonthlyIncomeChart(r.result.paymentIncome)):SnModal.error({title:"Algo salió mal",content:r.message})}).finally(e=>{SnFreeze.unFreeze("#reportIncomeTable")}))}}function reportIncomeToExcel(){let e=document.getElementById("reportIncomeTable");e&&TableToExcel(e.outerHTML,"Ingreso","Ingresos")}function buildIcomeChart(e=[]){let r=document.getElementById("icomeChart"),o=e.map((e,r)=>({label:e.app_plan_description,backgroundColor:Color(chartColors[r]).alpha(.5).rgbString(),borderColor:chartColors[r],fill:!1,data:[{x:"Enero",y:e.pay_1},{x:"Febrero",y:e.pay_2},{x:"Marzo",y:e.pay_3},{x:"Abril",y:e.pay_4},{x:"Mayo",y:e.pay_5},{x:"Junio",y:e.pay_6},{x:"Julio",y:e.pay_7},{x:"Agosto",y:e.pay_8},{x:"Septiembre",y:e.pay_9},{x:"Octubre",y:e.pay_10},{x:"Noviembre",y:e.pay_11},{x:"Diciembre",y:e.pay_12}]}));new Chart(r,{type:"line",data:{labels:["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],datasets:o},options:{maintainAspectRatio:!1,responsive:!0}})}function buildMonthlyIncomeChart(e){let r=document.getElementById("monthlyIncomeChart"),o=0,a=0,t=0,n=0,l=0,i=0,p=0,y=0,b=0,s=0,c=0,m=0;e.forEach(e=>{o+=parseFloat(e.pay_1),a+=parseFloat(e.pay_2),t+=parseFloat(e.pay_3),n+=parseFloat(e.pay_4),l+=parseFloat(e.pay_5),i+=parseFloat(e.pay_6),p+=parseFloat(e.pay_7),y+=parseFloat(e.pay_8),b+=parseFloat(e.pay_9),s+=parseFloat(e.pay_10),c+=parseFloat(e.pay_11),m+=parseFloat(e.pay_12)}),new Chart(r,{type:"line",data:{labels:["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],datasets:[{label:"Ingreso mensual",backgroundColor:Color("rgb(5, 101, 255)").alpha(.5).rgbString(),borderColor:"rgb(5, 101, 255)",fill:!1,data:[{x:"Enero",y:o},{x:"Febrero",y:a},{x:"Marzo",y:t},{x:"Abril",y:n},{x:"Mayo",y:l},{x:"Junio",y:i},{x:"Julio",y:p},{x:"Agosto",y:y},{x:"Septiembre",y:b},{x:"Octubre",y:s},{x:"Noviembre",y:c},{x:"Diciembre",y:m}]}]},options:{maintainAspectRatio:!1,responsive:!0}})}