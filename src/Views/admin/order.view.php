<div class="SnContent">
    <div class="SnGrid m-grid-4 col-gap">
        <div class="SnCard m-col-2 l-col-3 SnMb-4">
            <div class="SnCard-body">
                <div class="ProductSelect SnMb-5">
                    <?php foreach ($parameter['products'] as $key => $row) : ?>
                        <div class="ProductSelect-item SnBtn-group">
                            <div class="SnBtn">
                                <span><?php echo $row['title'] ?></span>
                                <span>S/. <?php echo $row['price'] ?></span>
                            </div>
                            <button class="SnBtn success" style="height: 100%;" onclick="addOrderItem(<?= $row['product_id'] ?>)"><i class="fas fa-plus"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="SnTable-wrapper">
                    <table class="SnTable" id="orderItemTable">
                        <thead>
                            <tr>
                                <th style="width: 80px;"></th>
                                <th></th>
                                <th>Item</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemTableBody"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;">Total</td>
                                <td><strong id="orderTotal" style="font-size: 1.3rem"></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="SnCard m-col-2 l-col-1 SnMb-4">
            <div class="SnCard-body">
                <div>
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
                            <textarea class="SnForm-control SnControl" id="orderObservation" cols="30" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div style="display: flex; justify-content: flex-end;">
                <div class="SnBtn lg" onclick="clearOrder()"><i class="fas fa-broom SnMr-2"></i>Limpiar</div>
                <div class="SnBtn primary lg SnMl-2" onclick="saveOrder()"><i class="far fa-save SnMr-2"></i>Procesar</div>
            </div>
        </div>
    </div>
</div>
<script>
    var products = <?= json_encode($parameter['products']) ?>;
</script>
<script src="<?= URL_PATH ?>/assets/build/script/admin/order.js"></script>

<div class="SnModal-wrapper" data-modal="ordenItemModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="ordenItemModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-network-wired SnMr-2"></i> Producto</div>
        <div class="SnModal-body">
            <form action="" class="SnForm" novalidate id="ordenItemForm" onsubmit="ordenItemSubmit(event)">
                <input type="hidden" class="SnForm-control" id="ordenItemUniqueId">
                <div class="SnForm-item required">
                    <label for="ordenItemObservation" class="SnForm-label">Observacion</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-file-code SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="ordenItemObservation" required>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="ordenItemUnitPrice" class="SnForm-label">Precio</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-coins SnControl-prefix"></i>
                        <input type="number" min="0" class="SnForm-control SnControl" id="ordenItemUnitPrice" required>
                    </div>
                </div>
                <button type="submit" class="SnBtn lg primary block" id="ordenItemFormSubmit"><i class="fas fa-save SnMr-2"></i>Guardar</button>
            </form>
        </div>
    </div>
</div>