<div class="SnTable-wrapper">
    <table class="SnTable" id="reportDeliveryCurrentTable">
        <thead>
            <tr>
                <th>Exibidor</th>
                <th>Cliente</th>
                <th>Fecha registro</th>
                <th>Fecha entrega</th>
                <th>Observacion</th>
                <th>Ubicación</th>
                <th>Usuario</th>
                <th>Total</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($parameter['deliverys']['data']) >= 1) : foreach ($parameter['deliverys']['data'] as $row) : ?>
                    <tr class="<?= $row['canceled'] == 1 ? 'canceled' : '' ?>">
                        <td><?= $row['exhibitor_code'] ?></td>
                        <td><?= $row['customer_social_reason'] ?></td>
                        <td><?= $row['date_of_issue'] ?></td>
                        <td><?= $row['date_of_delivery'] ?></td>
                        <td><?= $row['observation'] ?></td>
                        <td>
                            <a href="https://www.google.com/maps/search/?api=1&basemap=satellite&query=<?= explode(',',$row['lat_long'])[0] ?? '' ?>%2C<?= explode(',',$row['lat_long'])[1] ?? '' ?>" target="_blanck"><?= $row['lat_long'] ?></a>
                        </td>
                        <td><?= $row['user_name'] ?></td>
                        <td><?= $row['total'] ?></td>
                        <td>
                            <div class="SnBtn icon radio jsReportDeliveryOption" title="Anular" onclick="reportDeliveryCancel(<?= $row['delivery_id'] ?>)" <?= $row['canceled'] == 1 ? 'disabled' : '' ?>>
                                <i class="fas fa-ban"></i>
                            </div>
                        </td>
                        <td>
                            <div class="SnBtn icon radio jsReportDeliveryOption" title="Detalles" onclick="reportDeliveryItem(<?= $row['delivery_id'] ?>)">
                                <i class="fas fa-list-ul SnMr-2"></i>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;
            else : ?>
                <tr>
                    <td colspan="8">
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

<div class="TableFooter SnMt-3 SnMb-3">
    <div class="TableFooter-left">Mostrando: <span><?= count($parameter['deliverys']['data']) ?> de <?= $parameter['deliverys']['total'] ?></span></div>
    <div class="TableFooter-center">
        <?php
        $currentPage = $parameter['deliverys']['current'];
        $totalPage = $parameter['deliverys']['pages'];
        $limitPage = $parameter['deliverys']['limit'];
        $additionalQuery = '';
        $linksQuantity = 2;

        if ($totalPage > 1) {
            $lastPage       = $totalPage;
            $startPage      = (($currentPage - $linksQuantity) > 0) ? $currentPage - $linksQuantity : 1;
            $endPage        = (($currentPage + $linksQuantity) < $lastPage) ? $currentPage + $linksQuantity : $lastPage;

            $htmlPaginate       = '<nav aria-label="..."><ul class="SnPagination">';

            $class      = ($currentPage == 1) ? "disabled" : "";
            $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="reportDeliveryList(\'' . ($currentPage - 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link"><i class="fas fa-chevron-left"></i></a></li>';

            if ($startPage > 1) {
                $htmlPaginate   .= '<li class="SnPagination-item"><a href="#" onclick="reportDeliveryList(\'1\',\'' . $limitPage . '\')" class="SnPagination-link">1</a></li>';
                $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
            }

            for ($i = $startPage; $i <= $endPage; $i++) {
                $class  = ($currentPage == $i) ? "active" : "";
                $htmlPaginate   .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="reportDeliveryList(\'' . $i . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $i . '</a></li>';
            }

            if ($endPage < $lastPage) {
                $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
                $htmlPaginate   .= '<li><a href="#" onclick="reportDeliveryList(\'' . $lastPage . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $lastPage . '</a></li>';
            }

            $class      = ($currentPage == $lastPage || $totalPage == 0) ? "disabled" : "";
            $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="reportDeliveryList(\'' . ($currentPage + 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link"><i class="fas fa-chevron-right"></i></a></li>';

            $htmlPaginate       .= '</ul></nav>';

            echo  $htmlPaginate;
        }
        ?>
    </div>
    <div class="TableFooter-right">
        <select class="SnForm-control" onchange="reportDeliveryList(1,this.value)">
            <?php foreach ($variable = [10, 20, 50, 100] as $key => $value) : ?>
                <option value="<?= $value ?>" <?= $value == $parameter['deliverys']['limit'] ? 'selected' : '' ?>><?= $value ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>