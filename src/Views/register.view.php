<div class="MainContainer">
    <div class="Login SnCard">
        <div class="SnCard-body">
            <h1 class="Login-title">Crea una cuenta nueva</h1>
            <p class="Login-desc">Bienvenido a <?= APP_NAME ?></p>
            <?php require_once __DIR__ . '/partials/alertMessage.php' ?>
            <form action="" method="post" class="SnForm">
                <div class="SnForm-item required">
                    <label for="registerIdentityDocumentId" class="SnForm-label">Tipo de documento</label>
                    <select class="SnForm-control" required id="registerIdentityDocumentId" name="register[identityDocumentId]">
                        <?php foreach ($parameter['identityDocumentTypes'] as $key => $row) : ?>
                            <option value="<?= $row['identity_document_id'] ?>"><?= $row['description'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="SnForm-item required">
                    <label for="registerIdentityDocumentNumber" class="SnForm-label">Número de documento</label>
                    <div class="SnControlGroup">
                        <div class="SnControl-wrapper SnControlGroup-input">
                            <div class="SnControl-prefix"><i class="far fa-id-card"></i></div>
                            <input type="text" class="SnForm-control SnControl" required id="registerIdentityDocumentNumber" name="register[identityDocumentNumber]" placeholder="Nombre completo">
                        </div>
                        <div class="SnControlGroup-append">
                            <button type="button" class="SnBtn primary icon" onclick="getIdentityDocumentNumber()" id="registerSearchIdentityDocumentNumber"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="registerFullName" class="SnForm-label">Nombres</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-user SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" required id="registerFullName" name="register[fullName]" placeholder="Nombre completo">
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="registerLastName" class="SnForm-label">Apellidos</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-user SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" required id="registerLastName" name="register[lastName]" placeholder="Nombre completo">
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="registerEmail" class="SnForm-label">Email</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-envelope SnControl-prefix"></i>
                        <input type="email" class="SnForm-control SnControl" required id="registerEmail" name="register[email]" placeholder="Email">
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="registerUserName" class="SnForm-label">Nombre de usuario</label>
                    <div class="SnControl-wrapper">
                        <i class="far fa-user SnControl-prefix"></i>
                        <input type="text" class="SnForm-control SnControl" required id="registerUserName" name="register[userName]" placeholder="Nombre de usuario">
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="registerPassword" class="SnForm-label">Contraseña</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-key SnControl-prefix"></i>
                        <input type="password" class="SnForm-control SnControl" id="registerPassword" name="register[password]" placeholder="Contraseña">
                        <span class="SnControl-suffix far fa-eye togglePassword"></span>
                    </div>
                </div>
                <div class="SnForm-item required">
                    <label for="registerPasswordConfirm" class="SnForm-label">Confirmar contraseña</label>
                    <div class="SnControl-wrapper">
                        <i class="fas fa-key SnControl-prefix"></i>
                        <input type="password" class="SnForm-control SnControl" id="registerPasswordConfirm" name="register[passwordConfirm]" placeholder="Confirmar contraseña">
                        <span class="SnControl-suffix far fa-eye togglePassword"></span>
                    </div>
                </div>
                <input type="submit" value="Registrarse" name="commit" class="SnBtn primary radio lg block SnMb-5">
                <a href="<?= URL_PATH ?>/user/login" class="SnBtn radio block">Login</a>
            </form>
        </div>
    </div>
</div>

<script>
    function getIdentityDocumentNumber() {

        let documentNumber = document.getElementById('registerIdentityDocumentNumber');
        let documentId = document.getElementById('registerIdentityDocumentId');
        let search = document.getElementById('registerSearchIdentityDocumentNumber');

        search.classList.add('loading');
        RequestApi.fetch('/page/queryDocument', {
                method: 'POST',
                body: {
                    documentNumber: documentNumber.value,
                    documentTypeId: documentId.value,
                }
            })
            .then(res => {
                if (res.success) {
                    document.getElementById('registerFullName').value = res.result.full_name;
                    document.getElementById('registerLastName').value = `${res.result.father_last__name} ${res.result.mother_last_name}`;
                } else {
                    SnModal.error({
                        title: "Algo salió mal",
                        content: res.message
                    });
                }
            })
            .finally(e => {
                search.classList.remove('loading');
            });
    }
</script>