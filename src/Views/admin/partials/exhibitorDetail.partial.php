<?php if(count($parameter['exhibitor'])): ?>
    <strong>EXIBIDORA</strong>
    <input type="hidden" id="exhibitorId" value="<?= $parameter['exhibitor']['exhibitor_id'] ?>">
    <div>
        <strong>Codigo: </strong><?= $parameter['exhibitor']['code'] ?>
    </div>
    <div>
        <strong>Dirección: </strong><?= $parameter['exhibitor']['address'] ?>
    </div>
    <div>
        <strong>Localización: </strong><?= $parameter['exhibitor']['geo_name'] ?>
    </div>
    <div>
        <strong>Cliente: </strong><?= $parameter['exhibitor']['customer_social_reason'] ?>
    </div>
<?php endif; ?>