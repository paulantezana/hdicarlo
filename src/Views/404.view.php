<div class="Result">
    <h1 class="Result-title">404</h1>
    <?php if (isset($parameter['message']) && $parameter['message'] != '') : ?>
        <p class="Result-description"><?= $parameter['message'] ?></p>
    <?php else: ?>
        <p class="Result-description">Lo sentimos, la página que visitaste no existe</p>
    <?php endif; ?>
    <a href="<?= URL_PATH ?>/" class="SnBtn primary lg radio"><i class="fas fa-home SnMr-2"></i>Volver al Inicio</a>
</div>