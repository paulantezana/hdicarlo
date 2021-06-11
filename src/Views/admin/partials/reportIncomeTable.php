<div class="SnTable-wrapper">
    <table class="SnTable" id="planCurrentTable">
        <thead>
            <tr>
                <th>Exibidor</th>
                <th>Cliente</th>
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
            $delTotal = 0;
            if (count($parameter['incomes']) >= 1) : foreach ($parameter['incomes'] as $row) :
                    $month1 += $row['del_1'];
                    $month2 += $row['del_2'];
                    $month3 += $row['del_3'];
                    $month4 += $row['del_4'];
                    $month5 += $row['del_5'];
                    $month6 += $row['del_6'];
                    $month7 += $row['del_7'];
                    $month8 += $row['del_8'];
                    $month9 += $row['del_9'];
                    $month10 += $row['del_10'];
                    $month11 += $row['del_11'];
                    $month12 += $row['del_12'];
                    $delTotal += $row['del_total'];
            ?>
                    <tr>
                        <td><?= $row['exhibitor_code'] ?></td>
                        <td><?= $row['customer_social_reason'] ?></td>
                        <td><?= $row['del_1'] ?></td>
                        <td><?= $row['del_2'] ?></td>
                        <td><?= $row['del_3'] ?></td>
                        <td><?= $row['del_4'] ?></td>
                        <td><?= $row['del_5'] ?></td>
                        <td><?= $row['del_6'] ?></td>
                        <td><?= $row['del_7'] ?></td>
                        <td><?= $row['del_8'] ?></td>
                        <td><?= $row['del_9'] ?></td>
                        <td><?= $row['del_10'] ?></td>
                        <td><?= $row['del_11'] ?></td>
                        <td><?= $row['del_12'] ?></td>
                        <td><?= $row['del_total'] ?></td>
                    </tr>
                <?php endforeach;
            else : ?>
                <tr>
                    <td colspan="15">
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
                <td colspan="2">Totales</td>
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
                <td><?= $delTotal ?></td>
            </tr>
        </tfoot>
    </table>
</div>