<div class="SnTable-wrapper">
    <table class="SnTable" id="companyCurrentTable">
        <thead>
            <tr>
                <th>Logo</th>
                <th>RUC</th>
                <th>Razón social</th>
                <th>Razón comercial</th>
                <th>Reprecentante</th>
                <th>Telefono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Entorno</th>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php if (count($parameter['company']['data']) >= 1) : foreach ($parameter['company']['data'] as $row) : ?>
                    <tr class="<?= $row['state'] == 0 ? 'disabled' : '' ?>">
                        <td>
                            <div class="SnAvatar">
                                <?php if($row['logo'] !== ''): ?>
                                    <img class="SnAvatar-img" src="<?= URL_PATH ?><?= $row['logo'] ?>" alt="logo">
                                <?php else: ?>
                                    <div class="SnAvatar-text"><?= substr($row['document_number'], 0, 2); ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= $row['document_number'] ?></td>
                        <td><?= $row['social_reason'] ?></td>
                        <td><?= $row['commercial_reason'] ?></td>
                        <td><?= $row['representative'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['fiscal_address'] ?></td>
                        <td>
                            <?php if($row['development']==0): ?>
                                <div class="SnTag error">Desarrollo</div>
                            <?php else: ?>
                                <div class="SnTag success">Producción</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="SnBtn icon radio jsCompanyOption" <?= $row['state'] == 0 ? 'disabled' : '' ?> title="Logos" onclick="companyShowLogoModal(<?= $row['company_id'] ?>)">
                                <i class="fas fa-images"></i>
                            </div>
                        </td>
                        <td>
                            <div class="SnBtn icon radio jsCompanyOption" <?= $row['state'] == 0 ? 'disabled' : '' ?> title="Cambiar estado" onclick="companyChangeDevelopment(<?= $row['company_id'] ?>,<?= $row['development'] ?>)">
                                <i class="fas fa-terminal"></i>
                            </div>
                        </td>
                        <td>
                            <div class="SnBtn icon radio jsCompanyOption" <?= $row['state'] == 0 ? 'disabled' : '' ?> title="Editar" onclick="companyShowModalUpdate(<?= $row['company_id'] ?>)">
                                <i class="fas fa-pen"></i>
                            </div>
                        </td>
                        <td>
                            <a href="<?= URL_PATH ?>/inner/company/payment?companyId=<?= $row['company_id'] ?>" class="SnBtn icon radio jsCompanyOption" <?= $row['state'] == 0 ? 'disabled' : '' ?> title="Contratos">
                                <i class="fas fa-file-contract"></i>
                            </a>
                        </td>
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
<div class="TableFooter SnMt-3 SnMb-3">
    <div class="TableFooter-left">Mostrando: <span><?= count($parameter['company']['data']) ?> Empresas</span></div>
    <div class="TableFooter-center">
        <?php
            $currentPage = $parameter['company']['current'];
            $totalPage = $parameter['company']['pages'];
            $limitPage = $parameter['company']['limit'];
            $additionalQuery = '';
            $linksQuantity = 3;

            if ($totalPage > 1) {
                $lastPage       = $totalPage;
                $startPage      = (($currentPage - $linksQuantity) > 0) ? $currentPage - $linksQuantity : 1;
                $endPage        = (($currentPage + $linksQuantity) < $lastPage) ? $currentPage + $linksQuantity : $lastPage;

                $htmlPaginate       = '<nav aria-label="..."><ul class="SnPagination">';

                $class      = ($currentPage == 1) ? "disabled" : "";
                $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="companyList(\'' . ($currentPage - 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link"><i class="fas fa-chevron-left"></i></a></li>';

                if ($startPage > 1) {
                    $htmlPaginate   .= '<li class="SnPagination-item"><a href="#" onclick="companyList(\'1\',\'' . $limitPage . '\')" class="SnPagination-link">1</a></li>';
                    $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
                }

                for ($i = $startPage; $i <= $endPage; $i++) {
                    $class  = ($currentPage == $i) ? "active" : "";
                    $htmlPaginate   .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="companyList(\'' . $i . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $i . '</a></li>';
                }

                if ($endPage < $lastPage) {
                    $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
                    $htmlPaginate   .= '<li><a href="#" onclick="companyList(\'' . $lastPage . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $lastPage . '</a></li>';
                }

                $class      = ($currentPage == $lastPage || $totalPage == 0) ? "disabled" : "";
                $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="companyList(\'' . ($currentPage + 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link"><i class="fas fa-chevron-right"></i></a></li>';

                $htmlPaginate       .= '</ul></nav>';

                echo  $htmlPaginate;
            }
        ?>
    </div>
    <div class="TableFooter-right">Total empresas: <span><?= $parameter['company']['total'] ?></span></div>
</div>