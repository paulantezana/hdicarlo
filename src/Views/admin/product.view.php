<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>SERVICIOS</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn radio lg icon SnMr-2 jsProductAction" onclick="productToPrint()" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsProductAction" onclick="productToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn radio lg icon SnMr-2 jsProductAction" onclick="productList()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="SnBtn radio lg primary jsProductAction" onclick="productShowModalCreate()" title="Nuevo">
                <i class="fas fa-plus SnMr-2"></i> Nuevo
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div class="SnControl-wrapper SnMb-5">
                <input type="text" class="SnForm-control SnControl" id="searchContent" placeholder="Buscar...">
                <i class="SnControl-suffix fas fa-search"></i>
            </div>
            <div id="productTable"></div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/build/script/admin/product.js"></script>

<div class="SnModal-wrapper" data-modal="productModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="productModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-network-wired SnMr-2"></i> Product</div>
        <div class="SnModal-body">
            <form action="" class="SnForm" novalidate id="productForm" onsubmit="productSubmit(event)">
                <input type="hidden" class="SnForm-control" id="productId">
                <div class="SnForm-item required">
                    <label for="productBarCode" class="SnForm-label">Codigo</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-qrcode SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="productBarCode">
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="productTitle" class="SnForm-label">Titulo</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-file-code SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="productTitle" required>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="productPrice" class="SnForm-label">Precio</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-coins SnControl-prefix"></i>
                        <input type="number" min="0" class="SnForm-control SnControl" id="productPrice" required>
                    </div>
                </div>
                <button type="submit" class="SnBtn lg primary block" id="productFormSubmit"><i class="fas fa-save SnMr-2"></i>Guardar</button>
            </form>
        </div>
    </div>
</div>