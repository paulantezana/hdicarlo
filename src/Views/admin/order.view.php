<div class="SnContent">
    <div class="SnCard">
        <div class="SnCard-body">
            <div class="SnForm-item">
                <label for="searchContent" class="SnForm-label">Buscar por codigo</label>
                <div class="SnControl-wrapper">
                    <i class="fas fa-qrcode SnControl-prefix"></i>
                    <input type="text" class="SnForm-control SnControl" id="searchExhibitorCode" placeholder="Buscar...">
                </div>
            </div>

            <input type="hidden" id="currentLatitude">
            <input type="hidden" id="currentLongitude">
            <div id="exhibitorDetail" class="SnMb-4">
                <?php require_once(__DIR__ . '/partials/exhibitorDetail.partial.php'); ?>
            </div>

            <div class="SnForm-item">
                <label for="orderDateOfDelivery" class="SnForm-label">Fecha de entrega</label>
                <div class="SnControl-wrapper">
                    <i class="far fa-calendar SnControl-prefix"></i>
                    <input type="date" class="SnForm-control SnControl" id="orderDateOfDelivery">
                </div>
            </div>
            <div class="SnForm-item">
                <label for="orderObservation" class="SnForm-label">Observacion</label>
                <div class="SnControl-wrapper">
                    <i class="fas fa-sticky-note SnControl-prefix"></i>
                    <textarea class="SnForm-control SnControl" id="orderObservation" cols="30" rows="3"></textarea>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <div class="SnBtn lg" onclick="clearOrder()"><i class="fas fa-broom SnMr-2"></i>Limpiar</div>
                <div class="SnBtn primary lg SnMl-2" onclick="saveOrder()"><i class="far fa-save SnMr-2"></i>Procesar</div>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/script/order.js"></script>