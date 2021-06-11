<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>ENTREGAS</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn radio lg icon SnMr-2 jsReportDeliveryAction" onclick="printArea('reportDeliveryTable')" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsReportDeliveryAction" onclick="reportDeliveryToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsReportDeliveryAction" onclick="reportDeliveryList()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div class="SnGrid m-grid-4 col-gap row-gap">
                <div class="m-col-3 SnGrid m-grid-3 col-gap row-gap">
                    <div class="SnForm-item">
                        <input type="radio" name="filterByDate" id="filterByYear" value="1" onchange="filterByDateChange()">
                        <label for="filterByYear">Filtrar por año</label>
                    </div>
                    <div class="SnForm-item">
                        <input type="radio" name="filterByDate" id="filterByMonth" value="2" onchange="filterByDateChange()">
                        <label for="filterByMonth">Filtrar por mes</label>
                    </div>
                    <div class="SnForm-item">
                        <input type="radio" name="filterByDate" id="filterByDay" value="3" onchange="filterByDateChange()" checked>
                        <label for="filterByDay">Filtrar por dia</label>
                    </div>
                </div>
                <div>
                    <div class="SnForm-item inner hidden" id="filterYearWrapper">
                        <label for="filterYear" class="SnForm-label">Año</label>
                        <input type="number" id="filterYear" step="1" min="1900" max="3000" class="SnForm-control" onchange="reportIncomeList()" value="<?= date('Y') ?>">
                    </div>
                    <div class="SnForm-item inner hidden" id="filterMonthWrapper">
                        <label for="filterMonth" class="SnForm-label">Mes</label>
                        <input type="month" id="filterMonth" class="SnForm-control" onchange="reportIncomeList()" value="<?= date('Y-m') ?>">
                    </div>
                    <div class="SnForm-item inner" id="filterDayWrapper">
                        <label for="filterDay" class="SnForm-label">Dia</label>
                        <input type="date" id="filterDay" class="SnForm-control" onchange="reportIncomeList()" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
            </div>
            <div id="reportDeliveryTable" class="SnMb-5"></div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/build/script/admin/deliveryReport.js"></script>

<div class="SnModal-wrapper" data-modal="deliveryItemModalForm" data-maskclose="false">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="deliveryItemModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-shopping-basket SnMr-2"></i> Entrega detalle</div>
        <div class="SnModal-body" id="deliveryItemModalBody"></div>
    </div>
</div>