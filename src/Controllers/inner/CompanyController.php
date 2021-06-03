<?php

require_once(MODEL_PATH . '/Company.php');
require_once(MODEL_PATH . '/AppPlan.php');
require_once(MODEL_PATH . '/AppPaymentInterval.php');
require_once(MODEL_PATH . '/AppPayment.php');
require_once(MODEL_PATH . '/User.php');
require_once(MODEL_PATH . '/UserRole.php');
require_once(MODEL_PATH . '/AppAuthorization.php');
require_once(MODEL_PATH . '/IdentityDocumentType.php');

class CompanyController extends Controller
{
    private $connection;
    private $appAuthorizationModel;
    private $companyModel;
    private $userModel;
    private $appPaymentModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->appAuthorizationModel = new AppAuthorization($connection);
        $this->companyModel = new Company($connection);
        $this->userModel = new User($connection);
        $this->appPaymentModel = new AppPayment($connection);
    }

    public function home()
    {
        try {
            $identityDocumentTypeModel = new IdentityDocumentType($this->connection);
            $identityDocumentTypes = $identityDocumentTypeModel->getAll();

            $appPlanModel = new AppPlan($this->connection);
            $appPlan = $appPlanModel->getAll();

            $appPaymentIntervalModel = new AppPaymentInterval($this->connection);
            $appPaymentInterval = $appPaymentIntervalModel->getAll();

            $this->render('inner/company.view.php', [
                'identityDocumentTypes' => $identityDocumentTypes,
                'appPlan' => $appPlan,
                'appPaymentInterval' => $appPaymentInterval,
            ], 'layouts/inner.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/inner.layout.php');
        }
    }

    public function table()
    {
        $res = new Result();
        try {
            $page = htmlspecialchars(isset($_GET['page']) ? $_GET['page'] : 1);
            $limit = htmlspecialchars(isset($_GET['limit']) ? $_GET['limit'] : 20);
            $search = htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '');

            $company = $this->companyModel->paginate($page, $limit, $search);

            $res->view = $this->render('inner/partials/companyTable.php', [
                'company' => $company,
            ], '', true);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function id()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $res->result = $this->companyModel->getById($body['companyId']);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }


    public function create()
    {
        $res = new Result();
        $this->connection->beginTransaction();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body);
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $companyId = $this->companyModel->insert([
                'documentNumber' => htmlspecialchars($body['documentNumber']),
                'socialReason' => htmlspecialchars($body['socialReason']),
                'commercialReason' => htmlspecialchars($body['commercialReason']),
                'representative' => htmlspecialchars($body['representative']),
                'phone' => htmlspecialchars($body['phone']),
                'telephone' => htmlspecialchars($body['telephone']),
                'urlWeb' => htmlspecialchars($body['urlWeb']),
                'email' => htmlspecialchars($body['email']),
                'fiscalAddress' => htmlspecialchars($body['fiscalAddress']),
                'appPlanId' => htmlspecialchars($body['appPlanId']),
                'appPaymentIntervalId' => htmlspecialchars($body['appPaymentIntervalId']),
            ], $_SESSION[SESS_KEY]);

            // Role
            $userRoleModel = new UserRole($this->connection);
            $userRoleId = $userRoleModel->insert([
                'companyId' => $companyId,
                'description' => 'Administrador',
                'state' =>  true,
            ], $_SESSION[SESS_KEY]);

            $userId = $this->userModel->insert([
                'userName' => htmlspecialchars($body['documentNumber']),
                'email' => htmlspecialchars($body['email']),
                'password' => password_hash($body['userPassword'],PASSWORD_DEFAULT),
                'fullName' => htmlspecialchars($body['documentNumber']),
                'lastName' => htmlspecialchars($body['documentNumber']),
                'identityDocumentId' => 3,
                'identityDocumentNumber' => htmlspecialchars($body['documentNumber']),
                'userRoleId' => $userRoleId,
                'companyId' => $companyId,
            ], $_SESSION[SESS_KEY]);

            // Authorization
            $appAuthorization = $this->appAuthorizationModel->getAll();
            $authIds = [];
            foreach ($appAuthorization as $key => $appRow) {
                array_push($authIds, $appRow['app_authorization_id']);
            }
            $this->appAuthorizationModel->save($authIds, $userRoleId, $userId);

            $this->connection->commit();
            $res->success = true;
            $res->message = 'El registro se inserto exitosamente';
        } catch (Exception $e) {
            $this->connection->rollBack();
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function update()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, 'update');
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $currentDate = date('Y-m-d H:i:s');
            $this->companyModel->updateById($body['companyId'], [
                'document_number' => htmlspecialchars($body['documentNumber']),
                'social_reason' => htmlspecialchars($body['socialReason']),
                'commercial_reason' => htmlspecialchars($body['commercialReason']),
                'representative' => htmlspecialchars($body['representative']),
                'phone' => htmlspecialchars($body['phone']),
                'telephone' => htmlspecialchars($body['telephone']),
                'url_web' => htmlspecialchars($body['urlWeb']),
                'email' => htmlspecialchars($body['email']),
                'fiscal_address' => htmlspecialchars($body['fiscalAddress']),
                'app_plan_id' => htmlspecialchars($body['appPlanId']),
                'app_payment_interval_id' => htmlspecialchars($body['appPaymentIntervalId']),

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

    public function delete()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->companyModel->updateById($body['companyId'], [
                'state' => 0,

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);

            $res->success = true;
            $res->message = 'El registro se eliminó exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function changeDevelopment()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->companyModel->updateById($body['companyId'], [
                'development' => $body['development'],

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);

            $res->success = true;
            $res->message = 'El registro se actualizó exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function uploadLogoSquare()
    {
        $res = new Result();
        try {
            $companyId = $_POST['companyId'];

            if (isset($_FILES['logo'])) {
                $posterPath = uploadAndValidateFile($_FILES['logo'], '/Sn_' . $companyId . '/', 'logo_square', 102400, ['jpeg', 'jpg', 'png']);

                $currentDate = date('Y-m-d H:i:s');
                $this->companyModel->updateById($companyId, [
                    'logo' => $posterPath,

                    'updated_at' => $currentDate,
                    'updated_user_id' => $_SESSION[SESS_KEY],
                ]);
            } else {
                throw new Exception('No se especificó ningun archivo');
            }

            $res->success = true;
            $res->message = 'El registro se actualizo exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function uploadLogoLarge()
    {
        $res = new Result();
        try {
            $companyId = $_POST['companyId'];

            if (isset($_FILES['logo'])) {
                $posterPath = uploadAndValidateFile($_FILES['logo'], '/Sn_' . $companyId . '/', 'logo_large', 102400, ['jpeg', 'jpg', 'png']);

                $currentDate = date('Y-m-d H:i:s');
                $this->companyModel->updateById($companyId, [
                    'logo_large' => $posterPath,

                    'updated_at' => $currentDate,
                    'updated_user_id' => $_SESSION[SESS_KEY],
                ]);
            } else {
                throw new Exception('No se especificó ningun archivo');
            }

            $res->success = true;
            $res->message = 'El registro se actualizo exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function getAllPayment()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $companyId = $body['companyId'];

            $appPayment = $this->appPaymentModel->getAllByCompanyId($companyId);

            $res->view = $this->render('inner/partials/companyPaymentTable.php', [
                'appPayment' => $appPayment,
            ], '', true);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function payment(){
        try {
            $identityDocumentTypeModel = new IdentityDocumentType($this->connection);
            $identityDocumentTypes = $identityDocumentTypeModel->getAll();

            $appPlanModel = new AppPlan($this->connection);
            $appPlan = $appPlanModel->getAll();
            $companyId = $_GET['companyId'];

            $this->render('inner/companyPayment.view.php', [
                'appPlan' => $appPlan,
                'companyId' => $companyId,
            ], 'layouts/inner.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/inner.layout.php');
        }
    }

    public function getLastPayment()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $companyId = $body['companyId'];

            $lastPayment = $this->appPaymentModel->getLastPaymentByCompanyId($companyId);
            $company = $this->companyModel->getByIdPlan($companyId);

            $res->result = [
                'lastPayment' => $lastPayment,
                'company' => $company,
            ];
            $res->success = true;
            $res->message = 'El registro se actualizo exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function createPayment()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $this->appPaymentModel->updateAllIsLastToFalse($body['companyId']);

            $this->appPaymentModel->insert([
                'description'=> htmlspecialchars($body['description']),
                'reference'=> htmlspecialchars($body['reference']),
                'fromDatetime'=> htmlspecialchars($body['fromDatetime']),
                'toDatetime'=> htmlspecialchars($body['toDatetime']),
                'total'=> htmlspecialchars($body['total']),
                'companyId'=> htmlspecialchars($body['companyId']),
            ],$_SESSION[SESS_KEY]);

            $res->success = true;
            $res->message = 'El registro se insertó exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function canceledPayment(){
        $res = new Result();
        $this->connection->beginTransaction();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);
            $currentDate = date('Y-m-d H:i:s');

            $this->appPaymentModel->updateById($body['appPaymentId'], [
                'canceled'=> 1,
                'canceled_message'=> $body['message'],

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);

            $this->appPaymentModel->updateAllIsLastToFalse($body['companyId']);
            $this->appPaymentModel->setLastByCompanyId($body['companyId']);
            
            $this->connection->commit();
            $res->success = true;
            $res->message = 'El registro se anuló exitosamente';
        } catch (Exception $e) {
            $this->connection->rollBack();
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function validateInput($body, $type = 'create')
    {
        $res = new Result();
        $res->success = true;


        if ($type == 'create' || $type == 'update') {
            if (($body['documentNumber'] ?? '') == '') {
                $res->message .= 'Falta ingresar el número del documento | ';
                $res->success = false;
            }
            if (($body['socialReason'] ?? '') == '') {
                $res->message .= 'Falta ingresar la razón social | ';
                $res->success = false;
            }
            if (($body['commercialReason'] ?? '') == '') {
                $res->message .= 'Falta ingresar la razón comercial | ';
                $res->success = false;
            }
            if (($body['fiscalAddress'] ?? '') == '') {
                $res->message .= 'Falta ingresar la dirección | ';
                $res->success = false;
            }
            if ($type == 'create') {
                if (($body['userPassword'] ?? '') == '') {
                    $res->message .= 'Falta ingresar la contraseña | ';
                    $res->success = false;
                }
                if (($body['userPasswordConfirm'] ?? '') == '') {
                    $res->message .= 'Falta ingresar la confirmación contraseña | ';
                    $res->success = false;
                }
                if ($body['userPassword'] != $body['userPasswordConfirm']) {
                    $res->message .= 'Las contraseñas no coinciden | ';
                    $res->success = false;
                }
            }
        }

        if ($type == 'update') {
            if (($body['companyId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el id del company | ';
                $res->success = false;
            }
        }

        $res->message = trim(trim($res->message), '|');

        return $res;
    }
}
