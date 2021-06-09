<?php

require_once MODEL_PATH . '/AppAuthorization.php';
require_once MODEL_PATH . '/UserRole.php';

class AppAuthorizationController extends  Controller
{
    protected $connection;
    protected $appAuthorizationModel;
    protected $userRoleModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->appAuthorizationModel = new AppAuthorization($connection);
        $this->userRoleModel = new UserRole($connection);
    }

    public function home()
    {
        try {
            authorization($this->connection, 'rol');
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $appAuthorization = $this->listToTree($this->appAuthorizationModel->getAll());
            $userRole = $this->userRoleModel->getAll();

            $this->render('admin/appAuthorization.view.php', [
                'appAuthorization' => $appAuthorization,
                'userRole' => $userRole
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function byUserRoleId()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'rol_list');
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $res->result  = $this->appAuthorizationModel->getAllByUserRoleId($body['userRoleId']);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function save()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'rol_update');
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $authIds = $body['authIds'] ?? [];
            $userRoleId = $body['userRoleId'] ?? 0;

            $res->result  = $this->appAuthorizationModel->save($authIds, $userRoleId, $_SESSION[SESS_KEY]);
            $res->success = true;
            $res->message = 'Los cambios se guardaron exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    private function listToTree(array $list){
        $parents = [];
        foreach ($list as $key => $row) {
            if($row['parent_id'] == 0){
                $row['children'] = [];
                array_push($parents, $row);
            }
        }

        foreach ($parents as $key => $rowParent) {
            foreach ($list as $k => $r) {
                if($r['parent_id'] == $rowParent['app_authorization_id'] && $r['parent_id'] != 0){
                    array_push($parents[$key]['children'], $r);
                }
            }
        }

        return $parents;
    }
}
