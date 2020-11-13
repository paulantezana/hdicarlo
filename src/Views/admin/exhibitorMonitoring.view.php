<div class="SnContent">
    <div class="SnCard SnMb-5">
        <div class="SnCard-body">
            //sss
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div class="SnGrid m-grid-2 lg-grid-4 col-gap row-gap SnMb-5">
                
                <div class="SnControlGroup">
                    <div class="SnControlGroup-prepend">
                        <div class="SnBtn icon primary" id="customerSearchDocument" onclick="setDateMonitoring(-1)" ><i class="fas fa-minus"></i></div>
                    </div>
                    <div class="SnControlGroup-input">
                        <div class="SnControl-wrapper">
                            <i class="far fa-calendar-alt SnControl-prefix"></i>
                            <input type="date" class="SnForm-control SnControl" id="filterDateStart" onchange="getMonitoringData()" value="<?php echo date('Y-m-d', strtotime('-20 day')) ?>">
                        </div>
                        <!-- <input type="text" class="SnForm-control" id="customerDocumentNumber" readonly value="10"> -->
                    </div>
                    <div class="SnControlGroup-append">
                        <div class="SnBtn icon primary" id="customerSearchDocument" onclick="setDateMonitoring(1)" ><i class="fas fa-plus"></i></div>
                    </div>
                </div>

                <div class="SnControlGroup">
                    <div class="SnControlGroup-prepend">
                        <div class="SnBtn icon primary" id="customerSearchDocument" onclick="setDateMonitoring(-10)" ><i class="fas fa-minus"></i></div>
                    </div>
                    <div class="SnControlGroup-input">
                        <input type="text" class="SnForm-control" id="customerDocumentNumber" readonly value="10">
                    </div>
                    <div class="SnControlGroup-append">
                        <div class="SnBtn icon primary" id="customerSearchDocument" onclick="setDateMonitoring(10)" ><i class="fas fa-plus"></i></div>
                    </div>
                </div>

                <div class="SnControl-wrapper">
                    <i class="fas fa-list-ol SnControl-prefix"></i>
                    <select class="SnForm-control SnControl" id="filterQuantity" onchange="getMonitoringData()">
                        <?php for ($i=30; $i <= 100; $i+=10): ?>
                            <?php if($i === 10): ?>
                                <option value="<?= $i ?>" selected><?= $i ?></option>
                            <?php else: ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="SnTable-wrapper">
                <table class="SnTable MonitoringTable">
                    <thead id="monitoringTableHead"></thead>
                    <tbody id="monitoringTableBody"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/script/helpers/moment.js"></script>
<script src="<?= URL_PATH ?>/assets/script/exhibitorMonitoring.js"></script>