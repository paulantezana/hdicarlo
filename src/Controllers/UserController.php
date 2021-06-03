<?php

require_once(MODEL_PATH . '/Company.php');
require_once(MODEL_PATH . '/AppPayment.php');
require_once(MODEL_PATH . '/User.php');
require_once(MODEL_PATH . '/UserRole.php');
require_once(MODEL_PATH . '/UserForgot.php');
require_once(MODEL_PATH . '/AppAuthorization.php');
require_once(MODEL_PATH . '/IdentityDocumentType.php');
require_once(CERVICE_PATH . '/SendManager/EmailManager.php');

class UserController extends Controller
{
    private $connection;
    private $appAuthorizationModel;
    private $companyModel;
    private $userModel;
    private $appPaymentModel;
    private $userForgotModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->appAuthorizationModel = new AppAuthorization($connection);
        $this->companyModel = new Company($connection);
        $this->userModel = new User($connection);
        $this->appPaymentModel = new AppPayment($connection);
        $this->userForgotModel = new UserForgot($connection);
    }

    public function login()
    {
        try {
            if (isset($_POST['commit'])) {
                try {
                    if (!isset($_POST['email']) || !isset($_POST['password'])) {
                        throw new Exception('Los campos usuario y contraseña son requeridos');
                    }

                    $email = htmlspecialchars($_POST['email']);
                    $password = htmlspecialchars($_POST['password']);

                    if (empty($email) || empty($password)) {
                        throw new Exception('Los campos usuario y contraseña son requeridos');
                    }

                    $loginUser = $this->userModel->officeLogin($email);
                    if (!password_verify($password, $loginUser['password'])){
                        throw new Exception('El nombre de usuario y o contraseña es incorrecta.');
                    }

                    $responseApp = $this->initApp($loginUser);
                    if (!$responseApp->success) {
                        session_destroy();
                        $this->render('403.view.php', [
                            'message' => $responseApp->message,
                        ], 'layouts/site.layout.php');
                        return;
                    }

                    if ($loginUser['company_id'] > 0) {
                        $dateOfDue = $_SESSION[SESS_DATE_OF_DUE];
                        $currentDate = new DateTime(date("Y-m-d"));
                        $dateContract = new DateTime($dateOfDue);
                        if ($currentDate > $dateContract) {
                            session_destroy();
                            $this->render('expiredLicense.view.php', [
                                'dateContract' => $dateOfDue,
                            ], 'layouts/site.layout.php');
                            return;
                        }

                        $this->redirect('/admin');
                    } else {
                        $this->redirect('/');
                    }

                    $this->redirect('/admin');
                    return;
                } catch (Exception $e) {
                    $this->render('login.view.php', [
                        'messageType' => 'error',
                        'message' => $e->getMessage(),
                    ], 'layouts/site.layout.php');
                }
            } else {
                $this->render('login.view.php', [], 'layouts/site.layout.php');
            }
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function register()
    {
        try {
            $message = '';
            $messageType = '';
            $error = [];

            $identityDocumentTypeModel = new IdentityDocumentType($this->connection);
            $identityDocumentTypes = $identityDocumentTypeModel->getAll();

            if (isset($_POST['commit'])) {
                $this->connection->beginTransaction();
                try {
                    if (!isset($_POST['register'])) {
                        throw new Exception('No se proporcionó ningun dato');
                    }

                    $register = $_POST['register'];

                    $validate = $this->validateRegister($register);
                    if (!$validate->success) {
                        throw new Exception($validate->message);
                    }

                    $identityDocumentId = htmlspecialchars($register['identityDocumentId']);
                    $identityDocumentNumber = htmlspecialchars($register['identityDocumentNumber']);
                    $fullName = htmlspecialchars($register['fullName']);
                    $lastName = htmlspecialchars($register['lastName']);
                    $email = htmlspecialchars($register['email']);
                    $userName = htmlspecialchars($register['userName']);
                    $password = htmlspecialchars($register['password']);

                    $userId = $this->userModel->insert([
                        'userName' => $userName,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'fullName' => $fullName,
                        'lastName' => $lastName,
                        'identityDocumentId' => $identityDocumentId,
                        'identityDocumentNumber' => $identityDocumentNumber,
                        'userRoleId' => 0,
                        'companyId' => 0,
                    ], 0);

                    // Get All Data User
                    $loginUser = $this->userModel->getById($userId);
                    $responseApp = $this->initApp($loginUser);
                    if (!$responseApp->success) {
                        session_destroy();
                        $this->render('403.view.php', [
                            'message' => $responseApp->message,
                        ]);
                        return;
                    }

                    $urlApp = HOST . URL_PATH . '/user/login';
                    $resEmail = EmailManager::send(
                        APP_EMAIL,
                        $email,
                        '¡🚀 Bienvenido a ' . APP_NAME . ' !',
                        '<div>
                            <h1>' . $fullName . ', bienvenido(a) a ' . APP_NAME . '. venta de pasajes</h1>
                            <p>' . APP_DESCRIPTION . '</p>
                            <a href="' . $urlApp . '">Ingresar al sistema</a>
                        </div>'
                    );

                    if (!$resEmail->success) {
                        error_log('Send email register fail ' . $resEmail->message);
                    }

                    if ($loginUser['company_id'] > 0) {
                        $this->redirect('/user/postLogin');
                    } else {
                        $this->redirect('/');
                    }

                    $this->connection->commit();
                    return;
                } catch (Exception $e) {
                    $this->connection->rollBack();
                    $message = $e->getMessage();
                    $messageType = 'error';
                }
            }

            $this->render('register.view.php', [
                'identityDocumentTypes' => $identityDocumentTypes,
                'message' => $message,
                'error' => $error,
                'messageType' => $messageType,
            ], 'layouts/site.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function forgot()
    {
        if (isset($_SESSION[SESS_KEY])) {
            $this->redirect('/');
        }

        try {
            $resView = new Result();
            $resView->messageType = '';

            if (isset($_POST['commit'])) {
                try {
                    $email = htmlspecialchars($_POST['email'] ?? '');
                    if (($email) == '') {
                        throw new Exception('Falta ingresar el correo');
                    }

                    $user = $this->userModel->getBy('email', $email);
                    if (!$user) {
                        throw new Exception('Este correo electrónico no está registrado.');
                    }

                    $currentDate = date('Y-m-d H:i:s');
                    $token = sha1($currentDate . $user['user_id'] . $user['email']);

                    $this->userForgotModel->insert([
                        'secretKey' => $token,
                        'userId' => $user['user_id'],
                    ], $user['user_id']);

                    $urlForgot = HOST . URL_PATH . '/user/forgotValidate?key=' . $token;
                    $resEmail = EmailManager::send(
                        APP_EMAIL,
                        $user['email'],
                        'Recupera tu Contraseña',
                        '<p>Recientemente se solicitó un cambio de contraseña en tu cuenta. Si no fuiste tú, ignora este mensaje y sigue disfrutando de la experiencia de ' . APP_NAME . '.</p>
                                 <a href="' . $urlForgot . '" target="_blanck">Cambiar contraseña</a>'
                    );
                    if (!$resEmail->success) {
                        throw new Exception($resEmail->message);
                    }

                    $resView->message = 'El correo electrónico de confirmación de restablecimiento de contraseña se envió a su correo electrónico.';
                    $resView->messageType = 'success';
                } catch (Exception $exception) {
                    $resView->message = $exception->getMessage();
                    $resView->messageType = 'error';
                }
            }

            $this->render('forgot.view.php', [
                'message' => $resView->message,
                'messageType' => $resView->messageType,
            ], 'layouts/site.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function forgotValidate()
    {
        if (isset($_SESSION[SESS_KEY])) {
            $this->redirect('/');
        }

        try {
            $resView = new Result();
            $resView->messageType = '';
            $resView->contentType = 'validateToken';

            $user = [];
            $currentDate = date('Y-m-d H:i:s');

            // change password
            if (isset($_GET['key'])) {
                $resView->contentType = 'validateToken';
                $key = htmlspecialchars($_GET['key']);
                try {
                    $forgot = $this->userForgotModel->getBySecretKey($key);
                    if (!$forgot) {
                        throw new Exception('Token invalido o expirado');
                    }

                    $diff = strtotime($currentDate) - strtotime($forgot['created_at']);
                    if (($diff / 60) > 120) {
                        throw new Exception('Token expirado');
                    }
                    $user['user_id'] = $forgot['user_id'];
                    $user['user_forgot_id'] = $forgot['user_forgot_id'];

                    $resView->message = 'Token valido cambie su contraseña';
                    $resView->messageType = 'success';
                } catch (Exception $e) {
                    $resView->message = $e->getMessage();
                    $resView->messageType = 'error';
                }
            } else if (isset($_POST['commit'])) {
                $resView->contentType = 'changePassword';
                try {
                    $password = htmlspecialchars($_POST['password']);
                    $confirmPassword = htmlspecialchars($_POST['confirmPassword']);
                    $userForgotId = htmlspecialchars($_POST['userForgotId']);
                    $user['user_id'] = htmlspecialchars($_POST['userId']);

                    if (!($confirmPassword === $password)) {
                        throw new Exception('Las contraseñas no coinciden');
                    }
                    if (!$user['user_id']) {
                        throw new Exception('Usuario no especificado.');
                    }

                    $password = sha1($password);
                    $this->userModel->updateById($user['user_id'], [
                        "updated_at" => $currentDate,
                        "updated_user_id" => $user['user_id'],

                        'password' => $password,
                    ]);
                    $this->userForgotModel->updateById($userForgotId, [
                        "updated_at" => $currentDate,
                        "updated_user_id" => $user['user_id'],

                        'used' => 1,
                        'secret_key' => '',
                    ]);

                    $resView->message = 'Cambio de contraseña exitosa';
                    $resView->messageType = 'success';
                } catch (Exception $e) {
                    $resView->message = $e->getMessage();
                    $resView->messageType = 'error';
                }
            }

            $this->render('forgotValidate.view.php', [
                'message' => $resView->message,
                'messageType' => $resView->messageType,
                'contentType' => $resView->contentType,
                'user' => $user,
            ], 'layouts/site.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/user/login');
    }

    private function initApp($user)
    {
        $res = new Result();
        try {
            unset($user['password']);
            unset($user['updated_at']);
            unset($user['created_at']);
            unset($user['created_user_id']);
            unset($user['updated_user_id']);

            $_SESSION[SESS_KEY] = $user['user_id'];
            $_SESSION[SESS_USER] = $user;

            if (!($user['company_id'] > 0)) {
                $res->success = true;
                return $res;
            }

            $menu = $this->appAuthorizationModel->getMenu($user['user_role_id']);
            $appPayment = $this->appPaymentModel->getLastPaymentByCompanyId($user['company_id']);
            
            setcookie('admin_menu', json_encode($menu), time() + (86400000), '/');
            if($appPayment == false){
                $_SESSION[SESS_DATE_OF_DUE] = date('Y-m-d H:i:s', strtotime('-1 day'));
                $_SESSION[SESS_DATE_OF_DUE_DAY] = 0;
            } else {
                $_SESSION[SESS_DATE_OF_DUE] = $appPayment['to_date_time'];
                $_SESSION[SESS_DATE_OF_DUE_DAY] = 15;
            }

            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        return $res;
    }

    public function updateProfile()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, 'updateProfile');
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $currentDate = date('Y-m-d H:i:s');
            $this->userModel->updateById($body['userId'], [
                'email' => htmlspecialchars($body['email']),
                'user_name' => htmlspecialchars($body['userName']),
                'identity_document_id' => htmlspecialchars($body['identityDocumentId']),
                'identity_document_number' => htmlspecialchars($body['identityDocumentNumber']),
                'last_name' => htmlspecialchars($body['lastName']),
                'full_name' => htmlspecialchars($body['fullName']),

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);
            $res->success = true;
            $res->message = 'El registro se actualizo exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function updateProfilePassword()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, 'updatePassword');
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $currentDate = date('Y-m-d H:i:s');
            $this->userModel->updateById($body['userId'], [
                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],

                'password' => sha1(htmlspecialchars($body['password'])),
            ]);
            $res->success = true;
            $res->message = 'El registro se actualizo exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function updateProfileAvatar()
    {
        $res = new Result();
        try {
            if (!isset($_FILES['avatar'])) {
                throw new Exception('Archivo no seleccionado.');
            }

            $currentDate = date('Y-m-d H:i:s');
            $avatarUrl = uploadAndValidateFile($_FILES['avatar'], '/upload/user/', 'user-' . $_POST['userId'], 102400, ['jpeg', 'jpg', 'png']);
            $this->userModel->UpdateById($_POST['userId'], [
                "updated_at" => $currentDate,
                "updated_user_id" => $_SESSION[SESS_KEY],
                "avatar" => $avatarUrl,
            ]);

            $res->success = true;
            $res->message = 'La foto de perfil se subió exitosamente.';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function innerLogin()
    {
        try {
            if (isset($_POST['commit'])) {
                try {
                    if (!isset($_POST['email']) || !isset($_POST['password'])) {
                        throw new Exception('Los campos usuario y contraseña son requeridos');
                    }

                    $email = htmlspecialchars($_POST['email']);
                    $password = htmlspecialchars($_POST['password']);

                    if (empty($email) || empty($password)) {
                        throw new Exception('Los campos usuario y contraseña son requeridos');
                    }

                    $loginUser = $this->userModel->innerLogin($email, $password);
                    if (!password_verify($password, $loginUser['password'])){
                        throw new Exception('El nombre de usuario y o contraseña es incorrecta.');
                    }
                    unset($loginUser['password']);
                    
                    $_SESSION[SESS_KEY] = $loginUser['user_id'];
                    $_SESSION[SESS_USER] = $loginUser;

                    $this->redirect('/inner');
                    return;
                } catch (Exception $e) {
                    $this->render('innerLogin.view.php', [
                        'messageType' => 'error',
                        'message' => $e->getMessage(),
                    ], 'layouts/site.layout.php');
                }
            } else {
                $this->render('innerLogin.view.php', [], 'layouts/site.layout.php');
            }
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    public function update()
    {
        try {
            if (!isset($_SESSION[SESS_KEY])) {
                $this->redirect('/usuario/login');
            }

            $identityDocumentTypeModel = new IdentityDocumentType($this->connection);
            $identityDocumentTypes = $identityDocumentTypeModel->getAll();

            $user = $this->userModel->getById($_SESSION[SESS_KEY]);
            $this->render('profileUpdate.view.php', [
                'user' => $user,
                'identityDocumentTypes' => $identityDocumentTypes,
            ], 'layouts/site.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/site.layout.php');
        }
    }

    private function validateRegister($body)
    {
        $res = new Result();
        $res->success = true;
        if (($body['email'] ?? '') == '') {
            $res->success = false;
            $res->message .= 'Falta ingresar el correo electrónico';
        }
        if (($body['fullName'] ?? '') == '') {
            $res->success = false;
            $res->message .= 'Falta ingresar el nombre completo del usuario';
        }
        if (($body['userName'] ?? '') == '') {
            $res->success = false;
            $res->message .= 'Falta ingresar el nombre de usuario';
        }
        if (($body['password'] ?? '') == '') {
            $res->success = false;
            $res->message .= 'Falta ingresar la contraseña';
        }
        if (($body['passwordConfirm'] ?? '') == '') {
            $res->success = false;
            $res->message .= 'Falta ingresar la confirmación contraseña';
        }
        if ($body['password'] != $body['passwordConfirm']) {
            $res->success = false;
            $res->message .= 'Las contraseñas no coinciden';
        }

        return $res;
    }

    private function validateInput($body, $type = 'create')
    {
        $res = new Result();
        $res->success = true;

        if ($type == 'create' || $type == 'update' || $type == 'updateProfile') {
            if (($body['email'] ?? '') == '') {
                $res->message .= 'Falta ingresar el correo electrónico | ';
                $res->success = false;
            }

            if (($body['userName'] ?? '') == '') {
                $res->message .= 'Falta ingresar el nombre de usuario | ';
                $res->success = false;
            }

            if ($type != 'updateProfile') {
                if (($body['userRoleId'] ?? '') == '') {
                    $res->message .= 'Falta elegir un rol | ';
                    $res->success = false;
                }
            }
        }

        if ($type == 'update') {
            if (($body['userId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el userId | ';
                $res->success = false;
            }
        }

        if ($type == 'create' || $type == 'updatePassword') {
            if (($body['password'] ?? '') == '') {
                $res->message .= 'Falta ingresar la contraseña | ';
                $res->success = false;
            }
            if (($body['confirmPassword'] ?? '') == '') {
                $res->message .= 'Falta ingresar la confirmación contraseña | ';
                $res->success = false;
            }
            if ($body['password'] != $body['confirmPassword']) {
                $res->message .= 'Las contraseñas no coinciden | ';
                $res->success = false;
            }
        }

        return $res;
    }
}
