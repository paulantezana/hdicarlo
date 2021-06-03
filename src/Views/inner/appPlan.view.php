<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>PLANES</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn radio lg icon SnMr-2 jsAppPlanAction" onclick="appPlanToPrint()" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsAppPlanAction" onclick="appPlanToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsAppPlanAction" onclick="appPlanList()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="SnBtn radio lg primary jsAppPlanAction" onclick="appPlanShowModalCreate()" title="Nuevo">
                <i class="fas fa-plus SnMr-2"></i> Nuevo
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div id="appPlanTable"></div>
        </div>
    </div>
</div>

<script> var appPaymentInterval = <?= json_encode($parameter['appPaymentInterval']); ?>; </script>
<script src="<?= URL_PATH ?>/assets/build/script/inner/appPlan.js"></script>

<div class="SnModal-wrapper" data-modal="appPlanModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="appPlanModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-network-wired SnMr-2"></i> PLAN</div>
        <div class="SnModal-body">
            <form action="" class="SnForm" novalidate id="appPlanForm" onsubmit="appPlanSubmit(event)">
                <input type="hidden" class="SnForm-control" id="appPlanId">
                <div class="SnForm-item required">
                    <label for="appPlanDescripcion" class="SnForm-label">Descripcion</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-file-code SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="appPlanDescripcion" required>
                    </div>
                </div>
                <div>
                    <div class="SnTable-wrapper SnMb-5">
                        <table class="SnTable" id="appPlanIntervalTable">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Precio</th>
                                    <th style="width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody id="appPlanIntervalTableBody">
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="SnBtn radio block SnMb-5" onclick="addAppPlanInterval()"><i class="fas fa-plus SnMr-2"></i>Agregar documento</button>
                </div>
                <button type="submit" class="SnBtn lg primary block" id="appPlanFormSubmit"><i class="fas fa-save SnMr-2"></i>Guardar</button>
            </form>
        </div>
    </div>
</div>