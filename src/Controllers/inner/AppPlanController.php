<?php

require_once(MODEL_PATH . '/AppPlan.php');
require_once(MODEL_PATH . '/AppPaymentInterval.php');
require_once(MODEL_PATH . '/AppPlanInterval.php');

class AppPlanController extends Controller
{
    private $connection;
    private $appPlanModel;
    private $appPlanIntervalModel;
    private $appPaymentIntervalModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->appPlanModel = new AppPlan($connection);
        $this->appPlanIntervalModel = new AppPlanInterval($connection);
        $this->appPaymentIntervalModel = new AppPaymentInterval($connection);
    }

    public function home()
    {
        try {
            $appPaymentInterval = $this->appPaymentIntervalModel->getAll();
            $this->render('inner/appPlan.view.php', [
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

            $appPlan = $this->appPlanModel->paginate($page, $limit, $search);

            $res->view = $this->render('inner/partials/appPlanTable.php', [
                'appPlan' => $appPlan,
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

            $appPlan = $this->appPlanModel->getById($body['appPlanId']);
            $appPlan['interval'] = $this->appPlanIntervalModel->getAllByAppPlanId($body['appPlanId']);

            $res->result = $appPlan;
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
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body);
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $appPlanId = $res->result = $this->appPlanModel->insert([
                'description'=> htmlspecialchars($body['description']),
            ], $_SESSION[SESS_KEY]);

            foreach ($body['interval'] as $key => $row) {
                $this->appPlanIntervalModel->insert([
                    'appPlanId' => $appPlanId,
                    'appPaymentIntervalId' => $row['appPaymentIntervalId'],
                    'price' => $row['price'],
                ],$_SESSION[SESS_KEY]);
            }

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
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, 'update');
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $currentDate = date('Y-m-d H:i:s');
            $this->appPlanModel->updateById($body['appPlanId'], [
                'description'=> htmlspecialchars($body['description']),

                'updated_at' => $currentDate,
                'updated_user_id' => $_SESSION[SESS_KEY],
            ]);

            foreach ($body['interval'] as $key => $row) {
                if($row['appPlanIntervalId']>0){
                    $currentDate = date('Y-m-d H:i:s');
                    $this->appPlanIntervalModel->updateById($row['appPlanIntervalId'], [
                        'app_payment_interval_id' => $row['appPaymentIntervalId'],
                        'price' => htmlspecialchars($row['price']),

                        'updated_at' => $currentDate,
                        'updated_user_id' => $_SESSION[SESS_KEY],
                    ]);
                } else {
                    $this->appPlanIntervalModel->insert([
                        'appPlanId' => $body['appPlanId'],
                        'appPaymentIntervalId' => $row['appPaymentIntervalId'],
                        'price' => $row['price'],
                    ],$_SESSION[SESS_KEY]);
                }
            }

            $res->success = true;
            $res->message = 'El registro se actualizo exitosamente';
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function deleteAppPlanInterval()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->appPlanIntervalModel->updateById($body['appPlanIntervalId'], [
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

    public function delete()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->appPlanModel->updateById($body['appPlanId'], [
                'state'=> 0,

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

    public function validateInput($body, $type = 'create')
    {
        $res = new Result();
        $res->success = true;

        if ($type == 'create' || $type == 'update') {
            if (($body['description'] ?? '') == '') {
                $res->message .= 'Falta ingresar la descripción | ';
                $res->success = false;
            }
        }

        if ($type == 'update') {
            if (($body['appPlanId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el id del plan | ';
                $res->success = false;
            }
        }

        $res->message = trim(trim($res->message),'|');

        return $res;
    }
}
