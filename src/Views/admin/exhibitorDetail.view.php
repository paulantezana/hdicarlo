<div class="SnContent">
    <div class="SnGrid l-grid-4 col-gap row-gap SnMb-3">
        <div>
            <div class="SnCard SnMb-3">
                <div class="SnCard-body">
                    <strong>EXIBIDORA</strong>
                    <input type="hidden" id="exhibitorId" value="<?= $parameter['exhibitor']['exhibitor_id'] ?>">
                    <div>
                        <strong>Codigo: </strong> <?= $parameter['exhibitor']['code'] ?>
                    </div>
                    <div>
                        <strong>Dirección: </strong> <a href="#" onclick="exhibitorSetPositionMpas()"><?= $parameter['exhibitor']['address'] ?></a>
                    </div>
                    <div>
                        <strong>Localización: </strong> <?= $parameter['exhibitor']['geo_name'] ?>
                    </div>
                </div>
            </div>
            <div class="SnCard SnMb-3">
                <div class="SnCard-body">
                    <div class="SnMb-2">
                        <div>
                            <strong>DATOS DEL CLIENTE</strong>
                        </div>
                        <?php if(!empty($parameter['customer']['document_number'])): ?>
                            <strong>Número documento</strong>
                            <div><?= $parameter['customer']['document_number'] ?></div>
                        <?php endif; ?>
                        <?php if(!empty($parameter['customer']['social_reason'])): ?>
                            <strong>Rason social</strong>
                            <div><?= $parameter['customer']['social_reason'] ?></div>
                        <?php endif; ?>
                        <?php if(!empty($parameter['customer']['commercial_reason'])): ?>
                            <strong>Razón comercial</strong>
                            <div><?= $parameter['customer']['commercial_reason'] ?></div>
                        <?php endif; ?>
                        <?php if(!empty($parameter['customer']['fiscal_address'])): ?>
                            <strong>Dirección fiscal</strong>
                            <div><?= $parameter['customer']['fiscal_address'] ?></div>
                        <?php endif; ?>
                        <?php if(!empty($parameter['customer']['email'])): ?>
                            <strong>Email</strong>
                            <div>
                                <a target="_blanck" href="mailto:<?= $parameter['customer']['email'] ?>"><i class="far fa-envelope SnMr-2"></i><?= $parameter['customer']['email'] ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="SnCard">
                <div class="SnCard-body">
                    <a target="_blanck" href="tel:+51<?= $parameter['customer']['telephone'] ?>" class="SnBtn primary block SnMb-2"><i class="fas fa-phone-volume SnMr-2"></i><?= $parameter['customer']['telephone'] ?></a>
                    <a target="_blanck" href="http://api.whatsapp.com/send?phone=+51<?= $parameter['customer']['telephone'] ?>&lang=es" class="SnBtn success block"><i class="fab fa-whatsapp SnMr-2"></i><?= $parameter['customer']['telephone'] ?></a>
                </div>
            </div>
        </div>
        <div class="SnCard l-col-3">
            <div class="SnCard-body">
                <div id="exhibitorMap" style="min-height: 500px;" ></div>
                <input type="hidden" id="exhibitorLatLong" value="<?= $parameter['exhibitor']['lat_long'] ?>">
                <?php require_once __DIR__ . '/partials/googleMapsApi.partial.php'; ?>
            </div>
        </div>
    </div>
    <div class="SnGrid l-grid-4 col-gap row-gap SnMb-3">
        <div class="SnCard l-col-3">
            <div class="SnCard-body">
                <div id="exhibitorStates" class="InfiniteScroll"></div>
            </div>
        </div>
        <div class="SnCard">
            <div class="SnCard-body">
                <a href="<?= URL_PATH ?>/admin/order?exhibitorId=<?= $parameter['exhibitor']['exhibitor_id'] ?>" class="SnBtn block SnMb-2"><i class="fas fa-directions SnMr-2"></i>Pedido</a>
                <a href="<?= URL_PATH ?>/admin/delivery?exhibitorId=<?= $parameter['exhibitor']['exhibitor_id'] ?>" class="SnBtn block SnMb-2"><i class="fas fa-shopping-basket SnMr-2"></i>Entrega</a>
            </div>
        </div>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <a href="<?= URL_PATH ?>/admin/exhibitor/detail?exhibitorId=">
            <i class="fas fa-long-arrow-alt-left SnMr-2"></i><span>Atras</span>
            <div>ssssss</div>
        </a>
        <a href="<?= URL_PATH ?>/admin/exhibitor/detail?exhibitorId=">
            <i class="fas fa-long-arrow-alt-right SnMr-2"></i><span>Siguiente</span>
            <div>ssssss</div>
        </a>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/script/exhibitorDetail.js"></script>