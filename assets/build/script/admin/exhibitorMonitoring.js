function setDateMonitoring(t,e){let n=document.getElementById("filterDateStart"),i=addDay(n.value,t);n.value=i.format("YYYY-MM-DD"),getMonitoringData()}function getMonitoringData(){let t={};t.dateStart=document.getElementById("filterDateStart").value,t.quantity=document.getElementById("filterQuantity").value,RequestApi.fetch("/admin/exhibitor/getMonitoringData",{method:"POST",body:t}).then(e=>{e.success?buildMonitoringData(e.result,t):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(t=>{})}function buildMonitoringData(t,e){let n=t.exhibitor,i=t.exhibitorMonitoring,o=document.getElementById("monitoringTableHead"),r=buildDates(e.dateStart,e.quantity);if(o){o.innerHTML="";let t="";for(let e=0;e<r.length;e++){const n=r[e];t+=`<th class="${moment().format("YYYY-MM-DD")==n.format("YYYY-MM-DD")?"MonitoringTable-now":""}" title="${n.format("LL")}">${n.format("MMM")}<br>${n.format("D")}</th>`}o.insertAdjacentHTML("beforeend",`<tr>\n                                                                <th>Exibidora</th>\n                                                                <th>Cliente</th>\n                                                                ${t}</tr>\n                                                            `)}let a=document.getElementById("monitoringTableBody");if(a){a.innerHTML="";for(let t=0;t<n.length;t++){let e=n[t],i="";for(let t=0;t<r.length;t++)i+=`<td id="exhibitor__${e.exhibitor_id}__${r[t].format("YYYY-MM-DD")}"></td>`;a.insertAdjacentHTML("beforeend",`<tr>\n                                                                    <td id="exhibitor_${e.exhibitor_id}">${e.code}</td>\n                                                                    <td id="exhibitorCustomer_${e.exhibitor_id}">${e.customer_social_reason}</td>\n                                                                    ${i}\n                                                                </tr>`)}}for(let t=0;t<i.length;t++){const e=i[t];let n=document.getElementById(`exhibitor__${e.exhibitor_id}__${e.date_of_delivery}`);n||SnModal.error({title:"Algo salió mal",content:"Elemento no encontrado"}),n.classList.add("MonitoringTable-active"),n.setAttribute("title",`Fecha entrega: ${e.date_of_delivery} \n`+`Observacion: ${e.observation} \n`+`Usuario: ${e.user_name}`);let o=""==n.innerHTML?0:n.innerHTML;o=parseInt(o),n.innerHTML=o+=1}}function buildDates(t,e){let n=[];for(let i=0;i<e;i++){let e=addDay(t,i);n.push(e)}return n}function addDay(t,e){let n=moment(t);return n.add(e,"days"),n}document.addEventListener("DOMContentLoaded",()=>{getMonitoringData()});