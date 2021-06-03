<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>INGRESOS MENSUALES</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn radio lg icon SnMr-2 jsReportIncomeAction" onclick="printArea('reportIncomeTable')" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsReportIncomeAction" onclick="reportIncomeToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsReportIncomeAction" onclick="reportIncomeList()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div class="SnForm-item inner">
                <label for="currentYear" class="SnForm-label">Año</label>
                <input type="number" id="currentYear" step="1" min="1900" max="3000" class="SnForm-control" onchange="reportIncomeList()" value="<?= date('Y') ?>">
            </div>
            <div id="reportIncomeTable" class="SnMb-5"></div>
            <div style="height: 320px">
                <canvas id="icomeChart" width="320" height="320"></canvas>
            </div>
            <div style="height: 320px">
                <canvas id="monthlyIncomeChart" width="320" height="320"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/libraries/js/moment-with-locales.min.js"></script>
<script src="<?= URL_PATH ?>/assets/libraries/js/chart.min.js"></script>
<script src="<?= URL_PATH ?>/assets/build/script/inner/reportIncome.js"></script>
