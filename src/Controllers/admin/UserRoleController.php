<?php

require_once MODEL_PATH . '/UserRole.php';
require_once MODEL_PATH . '/AppAuthorization.php';

class UserRoleController extends Controller
{
    private $connection;
    private $userRoleModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->userRoleModel = new UserRole($connection);
    }

    public function index()
    {
        try {
            Authorization($this->connection, 'rol');
            $appAuthorizationModel = new AppAuthorization($this->connection);
            $appAuthorization = $appAuthorizationModel->getAll();

            $this->render('admin/role.view.php', [
                'appAuthorization' => $appAuthorization
            ],'layout/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.php', [
                'message' => $e->getMessage(),
            ],'layout/admin.layout.php');
        }
    }

    public function list()
    {
        $res = new Result();
        try {
            Authorization($this->connection, 'rol_list');

            $userRole = $this->userRoleModel->getAllWinDisabledByCompanyId($_SESSION[SESS_USER]['company_id']);
            $res->view = $this->render('admin/partials/roleList.php', [
                'userRole' => $userRole
            ],'',true);
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
            Authorization($this->connection, 'rol_list');
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);
            if (!$body) {
                echo '';
                return;
            }

            $res->result = $this->userRoleModel->getById((int) $body['userRoleId']);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function create()
    {
        $res = new Result();
        try {
            Authorization($this->connection, 'rol_create');
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body);
            if (!$validate->success) {
                echo json_encode($validate);
                return;
            }

            $body['companyId'] = $_SESSION[SESS_USER]['company_id'];
            $res->result = $this->userRoleModel->insert($body, $_SESSION[SESS_KEY]);
            $res->success = true;
            $res->message = 'El registro se inserto exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }
    public function update()
    {
        $res = new Result();
        try {
            Authorization($this->connection, 'rol_update');
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, true);
            if (!$validate->success) {
                echo json_encode($validate);
                return;
            }

            $currentDate = date('Y-m-d H:i:s');
            $res->result = $this->userRoleModel->updateById((int) $body['userRoleId'], [
                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
                'description' => $body['description'],
                'company_id' => $_SESSION[SESS_USER]['company_id'],
                'state' => $body['state'],
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
            Authorization($this->connection, 'rol_delete');

            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $res->result = $this->userRoleModel->deleteById((int) ($body['userRoleId'] ?? 0));
            $res->success = true;
            $res->message = 'El registro se eliminó exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function validateInput($body, $update = false)
    {
        $res = new Result();
        $res->success = true;

        if ($update) {
            if (($body['userRoleId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el userRoleId | ';
                $res->success = false;
            }
        }

        if (($body['description'] ?? '') == '') {
            $res->message .= 'Falta ingresar el nombre';
            $res->success = false;
        }

        return $res;
    }
}
