<div class="SnTable-wrapper" style="min-height: 230px">
    <table class="SnTable" id="exhibitorCurrentTable">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>N° Documento</th>
                <th>Razón comercial</th>
                <th>Tamaño</th>
                <th>Dirección</th>
                <th>Operativo</th>
                <th style="width: 100px"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parameter['exhibitor']['data'] as $row) : ?>
                <tr>
                    <td><?= $row['code'] ?></td>
                    <td><?= $row['customer_document_number'] ?></td>
                    <td><?= $row['customer_social_reason'] ?></td>
                    <td><?= $row['size_description'] ?></td>
                    <td><?= $row['address'] ?></td>
                    <td><?= $row['operative'] ?></td>
                    <td>
                        <div class="SnTable-action">
                            <div class="SnBtn icon jsExhibitorOption" title="Editar" onclick="exhibitorShowModalUpdate(<?= $row['exhibitor_id'] ?>)">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="SnDropdown">
                                <div class="SnDropdown-toggle SnBtn">
                                    <i class="fas fa-ellipsis-v"></i>
                                </div>
                                <ul class="SnDropdown-list" style="min-width: 300px">
                                    <li class="SnDropdown-item jsExhibitorOption" style="color: var(--snError);" title="Eliminar" onclick="exhibitorDelete(<?= $row['exhibitor_id'] ?>)">
                                        <i class="far fa-trash-alt SnMr-2"></i> Eliminar
                                    </li>
                                    <li class="SnDropdown-item jsExhibitorOption">
                                        <i class="fas fa-street-view SnMr-2"></i> Ubicar
                                    </li>
                                    <li class="SnDropdown-item jsExhibitorOption">
                                        <a href="<?= URL_PATH ?>/admin/exhibitor/detail?exhibitorId=<?= $row['exhibitor_id'] ?>">
                                            <i class="fas fa-angle-double-right SnMr-2"></i> Detalles
                                        </a>
                                    </li>
                                    <li class="SnDropdown-item jsExhibitorOption" onclick="exhibitorMaintenanceShowModal(<?= $row['exhibitor_id'] ?>)">
                                        <i class="fas fa-hammer SnMr-2"></i> Mantenimineto
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