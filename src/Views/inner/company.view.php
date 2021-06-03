<div class="SnContent">
    <div class="SnToolbar">
        <div class="SnToolbar-left">
            <i class=" fas fa-list-ul SnMr-2"></i> <strong>EMPRESAS</strong>
        </div>
        <div class="SnToolbar-right">
            <div class="SnBtn radio icon lg SnMr-2 jsCompanyAction" onclick="companyToPrint()" title="Imprimir">
                <i class="fas fa-print"></i>
            </div>
            <div class="SnBtn radio icon lg SnMr-2 jsCompanyAction" onclick="companyToExcel()" title="Exportar">
                <i class="fas fa-file-excel"></i>
            </div>
            <div class="SnBtn radio icon lg SnMr-2 jsCompanyAction" onclick="companyList()" title="Actualizar">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="SnBtn radio lg primary jsCompanyAction" onclick="companyShowModalCreate()" title="Nuevo">
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
            <div id="companyTable"></div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH ?>/assets/build/script/inner/company.js"></script>

<div class="SnModal-wrapper" data-modal="companyModalForm">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="companyModalForm">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="far fa-building SnMr-2"></i> Empresa</div>
        <div class="SnModal-body">
            <form action="" class="SnForm" novalidate id="companyForm" onsubmit="companySubmit(event)">
                <input type="hidden" class="SnForm-control" id="companyId">
                <div class="SnDivider">Empresa</div>
                <div class="SnForm-item required">
                    <label for="companyDocumentNumber" class="SnForm-label">Número documento</label>
                    <div class="SnControlGroup">
                        <div class="SnControl-wrapper SnControlGroup-input">
                            <i class="fas fa-credit-card SnControl-prefix"></i>
                            <input type="text" class="SnForm-control SnControl" id="companyDocumentNumber" required>
                        </div>
                        <div class="SnControlGroup-append">
                            <div class="SnBtn icon primary" id="companySearchDocument" onclick="companySearchDocument()"><i class="fas fa-search"></i></div>
                        </div>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="companySocialReason" class="SnForm-label">Razón social</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-user SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="companySocialReason" required>
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyCommercialReason" class="SnForm-label">Comercial social</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-user SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="companyCommercialReason">
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyFiscalAddress" class="SnForm-label">Dirección fiscal</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-street-view SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="companyFiscalAddress">
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyEmail" class="SnForm-label">Email</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-envelope SnControl-prefix"></i>
                        <input type="email" class="SnForm-control SnControl" id="companyEmail">
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyPhone" class="SnForm-label">Celular</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-mobile-alt SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="companyPhone">
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyTelephone" class="SnForm-label">Telefono</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-phone-volume SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="companyTelephone">
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyUrlWeb" class="SnForm-label">Url Web</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-globe-europe SnControl-prefix"></i>
                        <input type="url" class="SnForm-control SnControl" id="companyUrlWeb">
                    </div>
                </div>
                <div class="SnForm-item">
                    <label for="companyRepresentative" class="SnForm-label">Reprecentante</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-user SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" id="companyRepresentative">
                    </div>
                </div>

                <div class="SnDivider">Contrato</div>
                <div class="SnForm-item required">
                    <label for="companyAppPlanId" class="SnForm-label">App plan</label>
                    <select id="companyAppPlanId" class="SnForm-control" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($parameter['appPlan'] ?? [] as $row) : ?>
                            <option value="<?= $row['app_plan_id'] ?>"><?= $row['description'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="SnForm-item required">
                    <label for="companyAppPaymentIntervalId" class="SnForm-label">Frecuencia de pago</label>
                    <select id="companyAppPaymentIntervalId" class="SnForm-control" required>
                        <?php foreach ($parameter['appPaymentInterval'] ?? [] as $row) : ?>
                            <option value="<?= $row['app_payment_interval_id'] ?>"><?= $row['description'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="SnDivider">Usuario</div>
                <div class="SnForm-item required">
                    <label for="userPassword" class="SnForm-label">Contraseña</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-key SnControl-prefix"></i>
                        <input type="password" class="SnForm-control SnControl" id="userPassword" required>
                        <span class="SnControl-suffix far fa-eye togglePassword"></span>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="userPasswordConfirm" class="SnForm-label">Confirmar contraseña</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-key SnControl-prefix"></i>
                        <input type="password" class="SnForm-control SnControl" id="userPasswordConfirm" required>
                        <span class="SnControl-suffix far fa-eye togglePassword"></span>
                    </div>
                </div>
                <button type="submit" class="SnBtn primary lg block" id="companyFormSubmit"><i class="fas fa-save SnMr-2"></i> Guardar</button>
            </form>
        </div>
    </div>
</div>

<div class="SnModal-wrapper" data-modal="companyLogoModal">
    <div class="SnModal">
        <div class="SnModal-close" data-modalclose="companyLogoModal">
            <i class="fas fa-times"></i>
        </div>
        <div class="SnModal-header"><i class="far fa-building SnMr-2"></i> Empresa logo</div>
        <div class="SnModal-body">
            <input type="hidden" id="companyLogoId">
            <div class="SnUpload-warapper" id="companyLogoSquareWrapper">
                <div class="SnMb-5">
                    <img src="" alt="logo cuadrado" id="companyLogoSquareImg" style="width: 100%; display: block;">
                </div>
                <div class="SnForm-item">
                    <label class="SnForm-label" for="businessLogo">Logotipo en formato .JPG de (320px por 320px) menos de 100 KB </label>
                    <input type="file" class="SnForm-control" id="companyLogoSquare" accept="image/png,image/jpeg,image/jpg">
                </div>
                <button type="button" class="SnBtn lg primary block" onclick="uploadLogoSquare()"><i class="fas fa-cloud-upload-alt SnMr-2"></i>Subir logo</button>
            </div>
            <div class="SnUpload-warapper" id="companyLogoLargeWrapper">
                <div class="SnMb-5">
                    <img src="" alt="logo cuadrado" id="companyLogoLargeImg" style="width: 100%; display: block;">
                </div>
                <div class="SnForm-item">
                    <label class="SnForm-label" for="businessLogo">Logotipo en formato .JPG de (320px por 80px) menos de 100 KB </label>
                    <input type="file" class="SnForm-control" id="companyLogoLarge" accept="image/png,image/jpeg,image/jpg">
                </div>
                <button type="button" class="SnBtn lg primary block" onclick="uploadLogoLarge()"><i class="fas fa-cloud-upload-alt SnMr-2"></i>Subir logo</button>
            </div>
        </div>
    </div>
</div>