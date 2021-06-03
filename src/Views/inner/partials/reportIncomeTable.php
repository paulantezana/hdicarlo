<div class="SnTable-wrapper">
    <table class="SnTable" id="planCurrentTable">
        <thead>
            <tr>
                <th>Servidor</th>
                <th>Enero</th>
                <th>Febrero</th>
                <th>Marzo</th>
                <th>Abril</th>
                <th>Mayo</th>
                <th>Junio</th>
                <th>Julio</th>
                <th>Agosto</th>
                <th>Septiembre</th>
                <th>Octubre</th>
                <th>Noviembre</th>
                <th>Diciembre</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $month1 = 0;
            $month2 = 0;
            $month3 = 0;
            $month4 = 0;
            $month5 = 0;
            $month6 = 0;
            $month7 = 0;
            $month8 = 0;
            $month9 = 0;
            $month10 = 0;
            $month11 = 0;
            $month12 = 0;
            $payTotal = 0;
            if (count($parameter['paymentIncome']) >= 1) : foreach ($parameter['paymentIncome'] as $row) :
                    $month1 += $row['pay_1'];
                    $month2 += $row['pay_2'];
                    $month3 += $row['pay_3'];
                    $month4 += $row['pay_4'];
                    $month5 += $row['pay_5'];
                    $month6 += $row['pay_6'];
                    $month7 += $row['pay_7'];
                    $month8 += $row['pay_8'];
                    $month9 += $row['pay_9'];
                    $month10 += $row['pay_10'];
                    $month11 += $row['pay_11'];
                    $month12 += $row['pay_12'];
                    $payTotal += $row['pay_total'];
            ?>
                    <tr>
                        <td><?= $row['app_plan_description'] ?></td>
                        <td><?= $row['pay_1'] ?></td>
                        <td><?= $row['pay_2'] ?></td>
                        <td><?= $row['pay_3'] ?></td>
                        <td><?= $row['pay_4'] ?></td>
                        <td><?= $row['pay_5'] ?></td>
                        <td><?= $row['pay_6'] ?></td>
                        <td><?= $row['pay_7'] ?></td>
                        <td><?= $row['pay_8'] ?></td>
                        <td><?= $row['pay_9'] ?></td>
                        <td><?= $row['pay_10'] ?></td>
                        <td><?= $row['pay_11'] ?></td>
                        <td><?= $row['pay_12'] ?></td>
                        <td><?= $row['pay_total'] ?></td>
                    </tr>
                <?php endforeach;
            else : ?>
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
        <tfoot>
            <tr>
                <td>Totales</td>
                <td><?= $month1 ?></td>
                <td><?= $month2 ?></td>
                <td><?= $month3 ?></td>
                <td><?= $month4 ?></td>
                <td><?= $month5 ?></td>
                <td><?= $month6 ?></td>
                <td><?= $month7 ?></td>
                <td><?= $month8 ?></td>
                <td><?= $month9 ?></td>
                <td><?= $month10 ?></td>
                <td><?= $month11 ?></td>
                <td><?= $month12 ?></td>
                <td><?= $payTotal ?></td>
            </tr>
        </tfoot>
    </table>
</div>