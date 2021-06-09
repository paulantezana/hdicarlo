<div class="SnTable-wrapper">
    <table class="SnTable" id="productCurrentTable">
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Titúlo</th>
                <th>Precio</th>
                <th style="width: 100px"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($parameter['product']['data']) >= 1) : foreach ($parameter['product']['data'] as $row) : ?>
                    <tr>
                        <td><?= $row['bar_code'] ?></td>
                        <td><?= $row['title'] ?></td>
                        <td><?= $row['price'] ?></td>
                        <td>
                            <div class="SnTable-action">
                                <div class="SnBtn icon radio jsProductOption" title="Eliminar" onclick="productDelete(<?= $row['product_id'] ?>)">
                                    <i class="far fa-trash-alt"></i>
                                </div>
                                <div class="SnBtn icon radio jsProductOption" title="Editar" onclick="productShowModalUpdate(<?= $row['product_id'] ?>)">
                                    <i class="fas fa-pen"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;
            else : ?>
                <tr>
                    <td colspan="4">
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
    <div class="TableFooter-left">Mostrando: <span><?= count($parameter['product']['data']) ?> de <?= $parameter['product']['total'] ?></span></div>
    <div class="TableFooter-center">
        <?php
        $currentPage = $parameter['product']['current'];
        $totalPage = $parameter['product']['pages'];
        $limitPage = $parameter['product']['limit'];
        $additionalQuery = '';
        $linksQuantity = 2;

        if ($totalPage > 1) {
            $lastPage       = $totalPage;
            $startPage      = (($currentPage - $linksQuantity) > 0) ? $currentPage - $linksQuantity : 1;
            $endPage        = (($currentPage + $linksQuantity) < $lastPage) ? $currentPage + $linksQuantity : $lastPage;

            $htmlPaginate       = '<nav aria-label="..."><ul class="SnPagination">';

            $class      = ($currentPage == 1) ? "disabled" : "";
            $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="productList(\'' . ($currentPage - 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link"><i class="fas fa-chevron-left"></i></a></li>';

            if ($startPage > 1) {
                $htmlPaginate   .= '<li class="SnPagination-item"><a href="#" onclick="productList(\'1\',\'' . $limitPage . '\')" class="SnPagination-link">1</a></li>';
                $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
            }

            for ($i = $startPage; $i <= $endPage; $i++) {
                $class  = ($currentPage == $i) ? "active" : "";
                $htmlPaginate   .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="productList(\'' . $i . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $i . '</a></li>';
            }

            if ($endPage < $lastPage) {
                $htmlPaginate   .= '<li class="SnPagination-item disabled"><span class="SnPagination-link">...</span></li>';
                $htmlPaginate   .= '<li><a href="#" onclick="productList(\'' . $lastPage . '\',\'' . $limitPage . '\')" class="SnPagination-link">' . $lastPage . '</a></li>';
            }

            $class      = ($currentPage == $lastPage || $totalPage == 0) ? "disabled" : "";
            $htmlPaginate       .= '<li class="SnPagination-item ' . $class . '"><a href="#" onclick="productList(\'' . ($currentPage + 1) . '\',\'' . $limitPage . '\')" class="SnPagination-link"><i class="fas fa-chevron-right"></i></a></li>';

            $htmlPaginate       .= '</ul></nav>';

            echo  $htmlPaginate;
        }
        ?>
    </div>
    <div class="TableFooter-right">
        <select class="SnForm-control" onchange="productList(1,this.value)">
            <?php foreach ($variable = [10, 20, 50, 100] as $key => $value) : ?>
                <option value="<?= $value ?>" <?= $value == $parameter['product']['limit'] ? 'selected' : '' ?>><?= $value ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>