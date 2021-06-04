<div class="SnTable-wrapper" style="min-height: 230px">
    <table class="SnTable" id="exhibitorCurrentTable">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>N° Documento</th>
                <th>Razón comercial</th>
                <th>Tamaño</th>
                <th>Ciudad</th>
                <th>Dirección</th>
                <th style="width: 40px"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parameter['exhibitor']['data'] as $row) : ?>
                <tr>
                    <td><?= $row['code'] ?></td>
                    <td><?= $row['customer_document_number'] ?></td>
                    <td><?= $row['customer_social_reason'] ?></td>
                    <td><?= $row['size_description'] ?></td>
                    <td><?= $row['geo_name'] ?></td>
                    <td><a href="#" onclick="exhibitorSetPositionMpas('<?= $row['lat_long'] ?>')"><?= $row['address'] ?></a></td>
                    <td>
                        <div class="SnTable-action">
                            <div class="SnDropdown">
                                <div class="SnDropdown-toggle SnBtn sm icon">
                                    <i class="fas fa-ellipsis-v"></i>
                                </div>
                                <ul class="SnDropdown-list" style="min-width: 300px">
                                    <li class="SnDropdown-item jsExhibitorOption" onclick="exhibitorShowModalUpdate(<?= $row['exhibitor_id'] ?>)">
                                        <i class="fas fa-edit SnMr-2"></i> Editar
                                    </li>
                                    <li>
                                        <a class="SnDropdown-item jsExhibitorOption" href="<?= URL_PATH ?>/admin/exhibitor/detail?exhibitorId=<?= $row['exhibitor_id'] ?>">
                                            <i class="fas fa-angle-double-right SnMr-2"></i> Detalles
                                        </a>
                                    </li>
                                    <li class="SnDropdown-item jsExhibitorOption" onclick="exhibitorMaintenanceShowModal(<?= $row['exhibitor_id'] ?>)">
                                        <i class="fas fa-hammer SnMr-2"></i> Mantenimineto
                                    </li>
                                    <li class="SnDropdown-item jsExhibitorOption" style="color: var(--snError);" title="Eliminar" onclick="exhibitorDelete(<?= $row['exhibitor_id'] ?>)">
                                        <i class="far fa-trash-alt SnMr-2"></i> Eliminar
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$currentPage = $parameter['exhibitor']['current'];
$totalPage = $parameter['exhibitor']['pages'];
$limitPage = $parameter['exhibitor']['limit'];
$additionalQuery = '';
$linksQuantity = 3;

if ($totalPage > 1) {
    $lastPage       = $totalPage;
    $startPage      = (($currentPage - $linksQuantity) > 0) ? $currentPage - $linksQuantity : 1;
    $endPage        = (($currentPage + $linksQuantity) < $lastPage) ? $currentPage + $linksQuantity : $lastPage;

    $htmlPaginate       = '<nav aria-label="..."><ul class="SnPagination">';

    $class      = ($currentPage == 1) ? "disabled" : "";
    $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="exhibitorList(\'' . ($currentPage - 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link">Anterior</a></li>';

    if ($startPage > 1) {
        $htmlPaginate   .= '<li class="SnPagination-item"><a href="#" onclick="exhibitorList(\'1\',\'' . $limitPage . '\')" class="SnPagination-link">1</a></li>';
        $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        $class  = ($currentPage == $i) ? "active" : "";
        $htmlPaginate   .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="exhibitorList(\'' . $i . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $i . '</a></li>';
    }

    if ($endPage < $lastPage) {
        $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
        $htmlPaginate   .= '<li><a href="#" onclick="exhibitorList(\'' . $lastPage . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $lastPage . '</a></li>';
    }

    $class      = ($currentPage == $lastPage || $totalPage == 0) ? "disabled" : "";
    $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="exhibitorList(\'' . ($currentPage + 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link">Siguiente</a></li>';

    $htmlPaginate       .= '</ul></nav>';

    echo  $htmlPaginate;
}
?>