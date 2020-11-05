<div class="SnContent">
    <div class="SnGrid l-grid-4 col-gap row-gap SnMb-3">
        <div class="SnCard">
            <div class="SnCard-body">
                <strong>DETALLES</strong>
                <div>
                    <strong>Codigo: </strong> <?= $parameter['exhibitor']['code'] ?>
                </div>
                <div>
                    <strong>Dirección: </strong> <?= $parameter['exhibitor']['address'] ?>
                </div>
                <div>
                    <strong>Localización: </strong> <?= $parameter['exhibitor']['geo_name'] ?>
                </div>
                <div>
                    <strong>Cliente: </strong> <?= $parameter['exhibitor']['customer_social_reason'] ?>
                </div>
            </div>
        </div>
        <div class="SnCard l-col-3">
            <div class="SnCard-body">
                <div id="exhibitorMap" style="min-height: 500px;" ></div>
                <?php require_once __DIR__ . '/partials/googleMapsApi.partial.php'; ?>
            </div>
        </div>
    </div>
    <div class="SnGrid l-grid-4 col-gap row-gap SnMb-3">
        <div class="SnCard l-col-3">
            <div class="SnCard-body"></div>
        </div>
        <div class="SnCard">
            <div class="SnCard-body"></div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body"></div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/script/exhibitorDetail.js"></script>