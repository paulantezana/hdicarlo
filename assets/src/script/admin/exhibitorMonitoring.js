document.addEventListener("DOMContentLoaded", () => {
    getMonitoringData();
});

function setDateMonitoring(counter, type){
    let dateStart = document.getElementById('filterDateStart');
    let dateMoment = addDay(dateStart.value, counter);
    dateStart.value = dateMoment.format('YYYY-MM-DD');
    getMonitoringData();
}

function getMonitoringData() {
    let filter = {};
    filter.dateStart = document.getElementById('filterDateStart').value;
    filter.quantity = document.getElementById('filterQuantity').value;
    
    RequestApi.fetch('/admin/exhibitor/getMonitoringData', {
        method: "POST",
        body: filter,
    })
        .then((res) => {
            if (res.success) {
                buildMonitoringData(res.result, filter);
            } else {
                SnModal.error({ title: "Algo salió mal", content: res.message });
            }
        })
        .finally((e) => {
            // exhibitorSetLoading(false);
        });
}

function buildMonitoringData(data, filter){
    let exhibitors = data.exhibitor;
    let exhibitorMonitoring = data.exhibitorMonitoring;

    // HEAD
    let monitoringTableHead = document.getElementById('monitoringTableHead');
    let datesHead = buildDates(filter.dateStart, filter.quantity);
    if(monitoringTableHead){
        monitoringTableHead.innerHTML = '';
        let headTemplate = '';
        for (let i = 0; i < datesHead.length; i++) {
            const dateH = datesHead[i];
            let classNow = moment().format('YYYY-MM-DD') == dateH.format('YYYY-MM-DD') ? 'MonitoringTable-now' : '';
            headTemplate += `<th class="${classNow}" title="${dateH.format('LL')}">${dateH.format('MMM')}<br>${dateH.format('D')}</th>`;
        }
        monitoringTableHead.insertAdjacentHTML('beforeend', `<tr>
                                                                <th>Exibidora</th>
                                                                <th>Cliente</th>
                                                                ${headTemplate}</tr>
                                                            `);
    }

    // BODY
    let monitoringTableBody = document.getElementById('monitoringTableBody');
    if(monitoringTableBody){
        monitoringTableBody.innerHTML = '';
        for (let i = 0; i < exhibitors.length; i++) {
            let exhibitor = exhibitors[i];
            let resTD = '';
            for (let x = 0; x < datesHead.length; x++) {
                resTD += `<td id="exhibitor__${exhibitor.exhibitor_id}__${datesHead[x].format('YYYY-MM-DD')}"></td>`;
            }
            monitoringTableBody.insertAdjacentHTML('beforeend', `<tr>
                                                                    <td id="exhibitor_${exhibitor.exhibitor_id}">${exhibitor.code}</td>
                                                                    <td id="exhibitorCustomer_${exhibitor.exhibitor_id}">${exhibitor.customer_social_reason}</td>
                                                                    ${resTD}
                                                                </tr>`);
        }
    }

    // SET DATA
    for (let i = 0; i < exhibitorMonitoring.length; i++) {
        const monitoring = exhibitorMonitoring[i];
        // date_of_delivery
        let monitoringTD = document.getElementById(`exhibitor__${monitoring.exhibitor_id}__${monitoring.date_of_delivery}`);
        if(!monitoringTD){
            // console.log(monitoringTD,'monitoringTD',`exhibitor${monitoring.exhibitor_id}__${monitoring.date_of_delivery}`);
            SnModal.error({ title: "Algo salió mal", content: 'Elemento no encontrado' });
        }

        monitoringTD.classList.add('MonitoringTable-active');
        monitoringTD.setAttribute('title',`Fecha entrega: ${monitoring.date_of_delivery} \n` +
                                            `Observacion: ${monitoring.observation} \n` +
                                            `Usuario: ${monitoring.user_name}`);
        let oldValueContent = monitoringTD.innerHTML == '' ? 0 : monitoringTD.innerHTML;
        oldValueContent = parseInt(oldValueContent);
        monitoringTD.innerHTML = oldValueContent+=1;
    }
}


function buildDates(currentDate, cuantity){
    let dates = [];
    for (let i = 0; i < cuantity; i++) {
        let current = addDay(currentDate, i);
        dates.push(current);
    }
    return dates;
}

function addDay(currentDate, number){
    let current = moment(currentDate);
    // current.locale('es');
    current.add(number,'days');
    return current;
}

// function removeDay(currentDate, number){
//     let current = moment(currentDate);
//     // current.locale('es');
//     current.add(number,'days');
//     return current;
// }