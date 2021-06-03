document.addEventListener("DOMContentLoaded", () => {
    reportIncomeList();
});

let chartColors = [
    "rgb(255, 99, 132)", "rgb(255, 159, 64)", "rgb(255, 205, 86)", "rgb(75, 192, 192)", "rgb(54, 162, 235)", "rgb(153, 102, 255)", "rgb(201, 203, 207)",
    "rgb(255, 99, 132)", "rgb(255, 159, 64)", "rgb(255, 205, 86)", "rgb(75, 192, 192)", "rgb(54, 162, 235)", "rgb(153, 102, 255)", "rgb(201, 203, 207)",
];

function reportIncomeList() {
    let reportIncomeTable = document.getElementById("reportIncomeTable");
    let currentYear = document.getElementById("currentYear");
    if (currentYear) {
        if (!(currentYear.value > 1900 && currentYear.value < 3000)) {
            SnMessage.success({ content: 'Error usuario: Ingrese un año válido' });
            return;
        }

        if (reportIncomeTable) {
            SnFreeze.freeze({ selector: "#reportIncomeTable" });
            RequestApi.fetch(`/inner/report/incomeTable?year=${currentYear.value}`, {
                method: "GET",
            }
            )
                .then((res) => {
                    if (res.success) {
                        reportIncomeTable.innerHTML = res.view;
                        buildIcomeChart(res.result.paymentIncome);
                        buildMonthlyIncomeChart(res.result.paymentIncome);
                    } else {
                        SnModal.error({ title: "Algo salió mal", content: res.message });
                    }
                })
                .finally((e) => {
                    SnFreeze.unFreeze("#reportIncomeTable");
                });
        }
    }
}

function reportIncomeToExcel() {
    let dataTable = document.getElementById("reportIncomeTable");
    if (dataTable) {
        TableToExcel(dataTable.outerHTML, 'Ingreso', 'Ingresos');
    }
}

function buildIcomeChart(result = []) {
    let ctx = document.getElementById('icomeChart');

    let datasets = result.map((item, i) => {
        return ({
            label: item.app_plan_description,
            backgroundColor: Color(chartColors[i]).alpha(0.5).rgbString(),
            borderColor: chartColors[i],
            fill: false,
            data: [
                { x: 'Enero', y: item.pay_1 },
                { x: 'Febrero', y: item.pay_2 },
                { x: 'Marzo', y: item.pay_3 },
                { x: 'Abril', y: item.pay_4 },
                { x: 'Mayo', y: item.pay_5 },
                { x: 'Junio', y: item.pay_6 },
                { x: 'Julio', y: item.pay_7 },
                { x: 'Agosto', y: item.pay_8 },
                { x: 'Septiembre', y: item.pay_9 },
                { x: 'Octubre', y: item.pay_10 },
                { x: 'Noviembre', y: item.pay_11 },
                { x: 'Diciembre', y: item.pay_12 },
            ]
        })
    });

    new Chart(ctx, {
        type: "line",
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: datasets,
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
        },
    });
}

function buildMonthlyIncomeChart(result) {
    let ctx = document.getElementById('monthlyIncomeChart');

    let pay1 = 0, pay2 = 0, pay3 = 0, pay4 = 0, pay5 = 0, pay6 = 0, pay7 = 0, pay8 = 0, pay9 = 0, pay10 = 0, pay11 = 0, pay12 = 0;

    result.forEach(item => {
        pay1 += parseFloat(item.pay_1);
        pay2 += parseFloat(item.pay_2);
        pay3 += parseFloat(item.pay_3);
        pay4 += parseFloat(item.pay_4);
        pay5 += parseFloat(item.pay_5);
        pay6 += parseFloat(item.pay_6);
        pay7 += parseFloat(item.pay_7);
        pay8 += parseFloat(item.pay_8);
        pay9 += parseFloat(item.pay_9);
        pay10 += parseFloat(item.pay_10);
        pay11 += parseFloat(item.pay_11);
        pay12 += parseFloat(item.pay_12);
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
                        { x: 'Enero', y: pay1 },
                        { x: 'Febrero', y: pay2 },
                        { x: 'Marzo', y: pay3 },
                        { x: 'Abril', y: pay4 },
                        { x: 'Mayo', y: pay5 },
                        { x: 'Junio', y: pay6 },
                        { x: 'Julio', y: pay7 },
                        { x: 'Agosto', y: pay8 },
                        { x: 'Septiembre', y: pay9 },
                        { x: 'Octubre', y: pay10 },
                        { x: 'Noviembre', y: pay11 },
                        { x: 'Diciembre', y: pay12 },
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