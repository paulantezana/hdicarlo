
document.addEventListener("DOMContentLoaded", () => {
    reportIncomeList();
});

function filterByDateChange() {
    let filterByDate = document.querySelector('input[name="filterByDate"]:checked').value;
    let filterYearWrapper = document.getElementById('filterYearWrapper');
    let filterMonthWrapper = document.getElementById('filterMonthWrapper');
    let filterDayWrapper = document.getElementById('filterDayWrapper');

    filterYearWrapper.classList.add('hidden');
    filterMonthWrapper.classList.add('hidden');
    filterDayWrapper.classList.add('hidden');

    if (filterByDate == 1) {
        filterYearWrapper.classList.remove('hidden');
    } else if (filterByDate == 2) {
        filterMonthWrapper.classList.remove('hidden');
    } else if (filterByDate == 3) {
        filterDayWrapper.classList.remove('hidden');
    }

    reportIncomeList();
}

function reportIncomeList() {
    let reportIncomeTable = document.getElementById("reportIncomeTable");
    let filterByDate = document.querySelector('input[name="filterByDate"]:checked').value;
    let filterYear = document.getElementById("filterYear").value;
    let filterMonth = document.getElementById("filterMonth").value;
    let filterDay = document.getElementById("filterDay").value;


    // if (!(currentYear.value > 1900 && currentYear.value < 3000)) {
    //     SnMessage.success({ content: 'Error usuario: Ingrese un año válido' });
    //     return;
    // }

    if (reportIncomeTable) {
        SnFreeze.freeze({ selector: "#reportIncomeTable" });
        RequestApi.fetch('/admin/report/incomeReportTable', {
            method: "POST",
            body: { filterByDate, filterYear, filterMonth, filterDay }
        })
            .then((res) => {
                if (res.success) {
                    reportIncomeTable.innerHTML = res.view;
                    if (filterByDate == 1) {
                        
                    } else if (filterByDate == 2) {
                        buildMonthlyIncomeChart(res.result.incomes);
                    } else if (filterByDate == 3) {
                        
                    }
                    
                } else {
                    SnModal.error({ title: "Algo salió mal", content: res.message });
                }
            })
            .finally((e) => {
                SnFreeze.unFreeze("#reportIncomeTable");
            });
    }
}


function buildMonthlyIncomeChart(result) {
    let ctx = document.getElementById('reportIncomeChart');

    let del1 = 0, del2 = 0, del3 = 0, del4 = 0, del5 = 0, del6 = 0, del7 = 0, del8 = 0, del9 = 0, del10 = 0, del11 = 0, del12 = 0;

    result.forEach(item => {
        del1 += parseFloat(item.del_1);
        del2 += parseFloat(item.del_2);
        del3 += parseFloat(item.del_3);
        del4 += parseFloat(item.del_4);
        del5 += parseFloat(item.del_5);
        del6 += parseFloat(item.del_6);
        del7 += parseFloat(item.del_7);
        del8 += parseFloat(item.del_8);
        del9 += parseFloat(item.del_9);
        del10 += parseFloat(item.del_10);
        del11 += parseFloat(item.del_11);
        del12 += parseFloat(item.del_12);
    });

    new Chart(ctx, {
        type: "line",
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [
                {
                    label: 'Ingreso mensual',
                    backgroundColor: Color('rgb(5, 101, 255)').alpha(0.5).rgbString(),
                    borderColor: 'rgb(5, 101, 255)',
                    fill: false,
                    data: [
                        { x: 'Enero', y: del1 },
                        { x: 'Febrero', y: del2 },
                        { x: 'Marzo', y: del3 },
                        { x: 'Abril', y: del4 },
                        { x: 'Mayo', y: del5 },
                        { x: 'Junio', y: del6 },
                        { x: 'Julio', y: del7 },
                        { x: 'Agosto', y: del8 },
                        { x: 'Septiembre', y: del9 },
                        { x: 'Octubre', y: del10 },
                        { x: 'Noviembre', y: del11 },
                        { x: 'Diciembre', y: del12 },
                    ],
                }
            ],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
        },
    });
}