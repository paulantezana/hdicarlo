<div class="SnContentAside UserRole">
    <div class="SnContentAside-left SnMb-5">
        <div id="userRoleTable" class="SnMb-2"></div>
        <div title="Crear nuevo rol" style="border-style: dashed; line-height: 3.2em;" class="SnBtn block jsUserRoleOption" onclick="userRoleShowModalCreate()">
            <i class="fas fa-plus SnMr-2"></i>Agregar
        </div>
    </div>
    <div class="SnContentAside-right">
        <div class="SnCard">
            <div class="SnCard-body">
                <div class="SnMb-3">
                    <i class="fas fa-list-ul SnMr-2"></i>
                    <strong>Permisos del : </strong>
                    <span id="userRoleAuthTitle"></span>
                </div>
                <div id="userRoleAuthList">
                    <div class="SnTable-wrapper SnMb-5">
                        <table class="SnTable">
                            <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th style="width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parameter['appAuthorization'] ?? [] as $row) : ?>
                                    <tr data-id="<?= $row['app_authorization_id'] ?>">
                                        <td><strong><?= $row['description'] ?></strong></td>
                                        <td>
                                            <input type="checkbox" class="SnSwitch" id="autState<?= $row['app_authorization_id'] ?>">
                                        </td>
                                    </tr>
                                    <?php foreach ($row['children'] as $rowChildren) : ?>
                                        <tr data-id="<?= $rowChildren['app_authorization_id'] ?>">
                                            <td><i class="far fa-dot-circle SnMr-2 SnMl-2"></i> <?= $rowChildren['description'] ?></td>
                                            <td>
                                                <input type="checkbox" class="SnSwitch" id="autState<?= $rowChildren['app_authorization_id'] ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="SnBtn primary hidden lg block jsUserRoleOption" id="userRoleAuthSave" onclick="userRoleSaveAuthorization()"><i class="fas fa-save SnMr-2"></i>Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/build/script/admin/appAuthorization.js"></script>

<div class="SnModal-wrapper" data-modal="userRoleModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="userRoleModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="fas fa-user-tag SnMr-2"></i> Rol</div>
        <div class="SnModal-body">
            <form action="" class="SnForm" novalidate id="userRoleForm" onsubmit="userRoleSubmit()">
                <input type="hidden" class="SnForm-control" id="userRoleFormId">
                <div class="SnForm-item required">
                    <label for="userRoleDescription" class="SnForm-label">Descripcion</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-sticky-note SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="userRoleDescription" required>
                    </div>
                </div>
                <div class="SnForm-item">
                    <input type="checkbox" id="userRoleState" class="SnSwitch">
                    <label for="userRoleState">Estado</label>
                </div>
                <button type="submit" class="SnBtn primary lg block" id="userRoleFormSubmit"><i class="fas fa-save SnMr-2"></i>Guardar</button>
            </form>
        </div>
    </div>
</div>