<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>Exibidoras</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn jsExhibitorAction" onclick="exhibitorToPrint()" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn jsExhibitorAction" onclick="exhibitorToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn jsExhibitorAction" onclick="exhibitorList()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="SnBtn primary jsExhibitorAction" onclick="exhibitorShowModalCreate()" title="Nuevo">
                <i class="fas fa-plus SnMr-2"></i> Nuevo
            </div>
        </div>
    </div>
    <div class="SnCard SnMb-4">
        <div class="SnCard-body">
            <div class="SnMb-2">
                <div class="SnSwitch">
                    <input class="SnSwitch-control " id="filterOptions" type="checkbox" data-collapsetrigger="filterOptions">
                    <label class="SnSwitch-label" for="filterOptions">Filtro</label>
                </div>
            </div>
            <div class="SnCollapse" data-collapse="filterOptions">
                <div class="SnGrid m-grid-3 l-grid-4 col-gap row-gap lg-grid-5 xl-grid-6">
                    <div class="SnForm-item">
                        <label for="searchContent" class="SnForm-label">Buscar</label>
                        <div class="SnControl-wrapper">
                            <i class="fas fa-street-view SnControl-prefix"></i>
                            <input type="text" class="SnForm-control SnControl" id="searchContent" placeholder="Buscar...">
                        </div>
                    </div>
                    <div class="SnForm-item">
                        <label for="filterLocal1" class="SnForm-label">Departamento</label>
                        <select id="filterLocal1" class="SnForm-control">
                            <option value="">Seleccionar</option>
                            <?php foreach ($parameter['geoLevel1'] as $row): ?>
                                <option value="<?= $row['geo_level_1_id'] ?>"><?= $row['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="SnForm-item required">
                        <label for="filterLocal2" class="SnForm-label">Provincia</label>
                        <select id="filterLocal2" class="SnForm-control">
                            <option value="">Seleccionar</option>
                        </select>
                    </div>
                    <div class="SnForm-item required">
                        <label for="filterLocal3" class="SnForm-label">Distrito</label>
                        <select id="filterLocal3" class="SnForm-control">
                            <option value="">Seleccionar</option>
                        </select>
                    </div>
                    <div class="SnForm-item required">
                        <label for="filterCustomerId" class="SnForm-label">Cliente</label>
                        <select id="filterCustomerId">
                            <option value="">Seleccionar</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="SnCard">
        <div class="SnCard-body">
            <div class="Exhibitor">
                <div class="Exhibitor-table">
                    <div id="exhibitorTable"></div>
                </div>
                <div class="Exhibitor-map">
                    <div id="exhibitorGlobalMap" style="min-height: 500px;" ></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/script/exhibitor.js"></script>

<div class="SnModal-wrapper" data-modal="exhibitorModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="exhibitorModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-charging-station SnMr-2"></i> Exibidora</div>
        <div class="SnModal-body">
            <form action="" class="SnForm" novalidate id="exhibitorForm" onsubmit="exhibitorSubmit(event)">
                <input type="hidden" class="SnForm-control" id="exhibitorId">
                <div class="SnForm-item required">
                    <label for="exhibitorCode" class="SnForm-label">Codigo</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-qrcode SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="exhibitorCode" required>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="exhibitorSizeId" class="SnForm-label">Tamaño</label>
                    <select id="exhibitorSizeId" class="SnForm-control" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($parameter['size'] as $sizeRow): ?>
                            <option value="<?= $sizeRow['size_id'] ?>"><?= $sizeRow['description'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="SnForm-item required">
                    <label for="exhibitorCustomerId" class="SnForm-label">Cliente</label>
                    <div class="SnControlGroup">
                        <div class="SnControlGroup-input">
                            <select id="exhibitorCustomerId" required>
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                        <div class="SnControlGroup-append">
                            <div class="SnBtn icon primary" onclick="customerShowModalCreate()"><i class="fas fa-plus"></i></div>
                        </div>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="exhibitorGeoId" class="SnForm-label">Ciudad</label>
                    <select id="exhibitorGeoId" required>
                        <option value="">Seleccionar</option>
                    </select>
                </div>
                <div class="SnForm-item required">
                    <label for="exhibitorAddress" class="SnForm-label">Dirección</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-street-view SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="exhibitorAddress" required>
                    </div>
                </div>
                <div class="SnForm-item" style="height: 200px; position: relative;">
                    <?php require_once __DIR__ . '/partials/googleMapsApi.partial.php'; ?>
                    <input type="hidden" id="exhibitorLatitude">
                    <input type="hidden" id="exhibitorLongitude">
                </div>
                <button type="submit" class="SnBtn lg primary block" id="exhibitorFormSubmit"><i class="fas fa-save SnMr-2"></i>Guardar</button>
            </form>
        </div>
    </div>
</div>

<div class="SnModal-wrapper" data-modal="exhibitorMaintenanceModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="exhibitorMaintenanceModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-hammer SnMr-2"></i> Mantenimiento</div>
        <div class="SnModal-body"></div>
    </div>
</div>

<?php require_once (__DIR__ . '/partials/customerModalForm.partial.php') ?>