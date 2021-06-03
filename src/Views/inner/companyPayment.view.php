<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>PAGOS</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn radio lg icon SnMr-2 jsCompanyPaymentAction" onclick="companyPaymentToPrint()" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsCompanyPaymentAction" onclick="companyPaymentToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsCompanyPaymentAction" onclick="companyPaymentAll()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="SnBtn radio lg primary jsCompanyPaymentAction" onclick="companyPaymentShowModalCreate()" title="Nuevo">
                <i class="fas fa-plus SnMr-2"></i> Nuevo
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div id="companyPaymentTable"></div>
        </div>
    </div>
</div>

<script>
    var companyId = <?= $parameter['companyId']; ?>;
</script>
<script src="<?= URL_PATH ?>/assets/libraries/js/jspdf.min.js"></script>
<script src="<?= URL_PATH ?>/assets/libraries/js/moment-with-locales.min.js"></script>
<script src="<?= URL_PATH ?>/assets/build/script/inner/companyPayment.js"></script>

<div class="SnModal-wrapper" data-modal="companyPaymentModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="companyPaymentModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fab fa-paypal SnMr-2"></i> Pago</div>
        <div class="SnModal-body">
            <form action="" class="SnForm" novalidate id="companyPaymentForm" onsubmit="companyPaymentSubmit(event)">
                <input type="hidden" class="SnForm-control" id="companyPaymentId">
                <input type="hidden" class="SnForm-control" id="companyPaymentPrice">
                <div class="SnForm-item">
                    <label for="companyPaymentReference" class="SnForm-label">Folio</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-file-alt SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="companyPaymentReference">
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyPaymentFromDatetime" class="SnForm-label">Desde - Hasta</label>
                    <div class="SnControlGroup">
                        <div class="SnControl-wrapper SnControlGroup-input">
                            <i class="far fa-calendar-alt SnControl-prefix"></i>
                            <input type="date" class="SnForm-control SnControl" id="companyPaymentFromDatetime" required disabled>
                        </div>
                        <div class="SnControl-wrapper SnControlGroup-input">
                            <i class="far fa-calendar-alt SnControl-prefix"></i>
                            <input type="date" class="SnForm-control SnControl" id="companyPaymentToDatetime" required disabled>
                        </div>
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyPaymentDescription" class="SnForm-label">Descripción</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-file-contract SnControl-prefix"></i>
                        <textarea id="companyPaymentDescription" cols="30" rows="2" class="SnForm-control SnControl"></textarea>
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyPaymentTotal" class="SnForm-label">Total</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-coins SnControl-prefix"></i>
                        <input type="number" min="0" class="SnForm-control SnControl" id="companyPaymentTotal">
                    </div>
                </div>
                <button type="submit" class="SnBtn lg primary block" id="companyPaymentFormSubmit"><i class="fas fa-save SnMr-2"></i>Guardar</button>
            </form>
        </div>
    </div>
</div>