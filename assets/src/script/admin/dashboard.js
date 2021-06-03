document.addEventListener("DOMContentLoaded", () => {
    ordersChart();
    deliveryChart();
    document.getElementById("chartStartDate").addEventListener("change", () => {
        ordersChart();
        deliveryChart();
    });
    document.getElementById("chartEndDate").addEventListener("change", () => {
        ordersChart();
        deliveryChart();
    });
});

let chartColors = {
    red: "rgb(255, 99, 132)",
    orange: "rgb(255, 159, 64)",
    yellow: "rgb(255, 205, 86)",
    green: "rgb(75, 192, 192)",
    blue: "rgb(54, 162, 235)",
    purple: "rgb(153, 102, 255)",
    grey: "rgb(201, 203, 207)",
};

function ordersChart() {
    let startDate = document.getElementById("chartStartDate").value;
    let endDate = document.getElementById("chartEndDate").value;

    SnFreeze.freeze({ selector: '#filterWrapper' });
    RequestApi.fetch("/admin/report/orderChart", {
        method: "POST",
        body: {
            startDate,
            endDate,
        },
    }).then((res) => {
        if (res.success) {
            buildOrdersChart(res.result);
        } else {
            SnModal.error({ title: "Algo salió mal", content: res.message });
        }
    })
        .finally((e) => {
            SnFreeze.unFreeze();
        });
}

function deliveryChart() {
    let startDate = document.getElementById("chartStartDate").value;
    let endDate = document.getElementById("chartEndDate").value;

    SnFreeze.freeze({ selector: '#filterWrapper' });
    RequestApi.fetch("/admin/report/deliveryChart", {
        method: "POST",
        body: {
            startDate,
            endDate,
        },
    }).then((res) => {
        if (res.success) {
            buildDeliveryChart(res.result);
        } else {
            SnModal.error({ title: "Algo salió mal", content: res.message });
        }
    })
        .finally((e) => {
            SnFreeze.unFreeze();
        });
}

function buildOrdersChart(result) {
    let ctx = document.getElementById("ordersChart");
    new Chart(ctx, {
        type: "line",
        data: {
            datasets: [
                {
                    label: "Ordenes",
                    backgroundColor: Color(chartColors.red).alpha(0.5).rgbString(),
                    borderColor: chartColors.red,
                    data: [...result].map((item) => ({
                        x: item.created_at_query,
                        y: item.count,
                    })),
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                xAxes: [
                    {
                        type: "time",
                        time: {
                            parser: "YYYY-MM-DD",
                            round: "day",
                            tooltipFormat: "ll",
                        },
                    },
                ],
            },
        },
    });
}

function buildDeliveryChart(result) {
    let ctx = document.getElementById('deliveryChart');

    new Chart(ctx, {
        type: "line",
        data: {
            datasets: [
                {
                    label: "Entregas",
                    backgroundColor: Color(chartColors.red).alpha(0.5).rgbString(),
                    borderColor: chartColors.red,
                    data: [...result].map((item) => ({
                        x: item.created_at_query,
                        y: item.count,
                    })),
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                xAxes: [
                    {
                        type: "time",
                        time: {
                            parser: "YYYY-MM-DD",
                            round: "day",
                            tooltipFormat: "ll",
                        },
                    },
                ],
            },
        },
    });
}