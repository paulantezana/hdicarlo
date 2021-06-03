<div class="SnTable-wrapper">
    <table class="SnTable" id="paymentCurrentTable">
        <thead>
            <tr>
                <th>Cod</th>
                <th>Fecha pago</th>
                <th>Folio</th>
                <th>Descripción</th>
                <th>Desde</th>
                <th>Hasta</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th style="width: 50px"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($parameter['appPayment']) >= 1) : $paymentTotal = 0;
                foreach ($parameter['appPayment'] as $row) : $paymentTotal += ($row['canceled'] == 0 ? $row['total'] : 0); ?>
                    <tr class="<?= $row['canceled'] == 1 ? 'canceled' : '' ?>">
                        <td><?= $row['number'] ?></td>
                        <td><?= $row['date_time_of_issue'] ?></td>
                        <td><?= $row['reference'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['from_date_time'] ?></td>
                        <td><?= $row['to_date_time'] ?></td>
                        <td><?= $row['total'] ?></td>
                        <td title="<?= $row['canceled_message'] ?>"><span class="SnTag <?= $row['canceled'] == 0 ? 'success' : 'error' ?>"><?= $row['canceled'] == 0 ? 'activo' : 'anulado' ?></span></td>
                        <td><?= $row['user_name'] ?></td>
                        <td>
                            <div class="SnTable-action">
                                <button class="SnBtn icon radio jsPaymentOption" title="Anular" onclick="companyPaymentCanceled(<?= $row['app_payment_id'] ?>,<?= $row['number'] ?>)" <?= $row['canceled'] == 1 ? 'disabled' : '' ?>>
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" style="text-align: right;">Total</td>
                    <td><?= $paymentTotal ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php else : ?>
                <tr>
                    <td colspan="14">
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