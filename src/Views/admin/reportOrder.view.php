<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>ORDENES</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn radio lg icon SnMr-2 jsReportOrderAction" onclick="printArea('reportOrderTable')" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsReportOrderAction" onclick="reportOrderToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsReportOrderAction" onclick="reportOrderList()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div class="SnForm-item inner">
                <label for="currentYear" class="SnForm-label">Año</label>
                <input type="number" id="currentYear" step="1" min="1900" max="3000" class="SnForm-control" onchange="reportOrderList()" value="<?= date('Y') ?>">
            </div>
            <div id="reportOrderTable" class="SnMb-5"></div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/build/script/admin/orderReport.js"></script>

<div class="SnModal-wrapper" data-modal="orderItemModalForm" data-maskclose="false">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="orderItemModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-dolly SnMr-2"></i> Ordene detalle</div>
        <div class="SnModal-body" id="orderItemModalBody"></div>
    </div>
</div>
