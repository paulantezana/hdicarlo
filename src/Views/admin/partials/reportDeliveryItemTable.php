<div class="SnTable-wrapper">
    <table class="SnTable" id="entrustCurrentTable">
        <thead>
            <th>Cantidad</th>
            <th>Precio unitario</th>
            <th>Descripcion</th>
            <th>Observacion</th>
            <th>Total</th>
        </thead>
        <tbody>
            <?php if (count($parameter['deliveryItem']) >= 1) : foreach ($parameter['deliveryItem'] as $row) : ?>
                    <tr>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['unit_price'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['observation'] ?></td>
                        <td><?= $row['total'] ?></td>
                    </tr>
                <?php endforeach;
            else : ?>
                <tr>
                    <td colspan="13">
                        <div class="SnEmpty">
                            <img src="<?= URL_PATH . '/assets/images/empty.svg' ?>" alt="">
                            <div>No hay datos</div>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>