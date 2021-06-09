<?php

require_once(MODEL_PATH . '/Exhibitor.php');
require_once(MODEL_PATH . '/ExhibitorHistory.php');
require_once(MODEL_PATH . '/Size.php');
require_once(MODEL_PATH . '/Customer.php');
require_once(MODEL_PATH . '/GeoLocation.php');
require_once(MODEL_PATH . '/IdentityDocumentType.php');

class ExhibitorController extends Controller
{
    private $connection;
    private $exhibitorModel;
    private $exhibitorHistoryModel;
    private $geoLocationModel;
    private $customerModel;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->exhibitorModel = new Exhibitor($connection);
        $this->exhibitorHistoryModel = new ExhibitorHistory($connection);
        $this->geoLocationModel = new GeoLocation($connection);
        $this->customerModel = new Customer($connection);
    }

    public function home()
    {
        try {
            authorization($this->connection, 'exhibitor');
            $sizeModel = new Size($this->connection);
            $size = $sizeModel->getAll();

            $identityDocumentTypeModel = new IdentityDocumentType($this->connection);
            $identityDocumentType = $identityDocumentTypeModel->getAll();

            $customer = $this->customerModel->getAll();

            $this->render('admin/exhibitor.view.php', [
                'size' => $size,
                'customer' => $customer,
                'identityDocumentType' => $identityDocumentType,
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function detail()
    {
        try {
            authorization($this->connection, 'exhibitor');

            $exhibitorId = $_GET['exhibitorId'] ?? 0;
            if ($exhibitorId == 0) {
                $this->redirect('/admin');
            }

            $exhibitor = $this->exhibitorModel->getById($exhibitorId);
            $customer = $this->customerModel->getById($exhibitor['customer_id']);

            $this->render('admin/exhibitorDetail.view.php', [
                'exhibitor' => $exhibitor,
                'customer' => $customer,
            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }

    public function monitoring(){
        try {
            // $exhibitorId = $_GET['exhibitorId'] ?? 0;
            // if ($exhibitorId == 0) {
            //     $this->redirect('/admin');
            // }

            // $exhibitor = $this->exhibitorModel->getById($exhibitorId);
            // $customer = $this->customerModel->getById($exhibitor['customer_id']);

            $this->render('admin/exhibitorMonitoring.view.php', [

            ], 'layouts/admin.layout.php');
        } catch (Exception $e) {
            $this->render('500.view.php', [
                'message' => $e->getMessage(),
            ], 'layouts/admin.layout.php');
        }
    }
    public function getMonitoringData(){
        $res = new Result();
        try {
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);
            $companyId = $_SESSION[SESS_USER]['company_id'];

            if(!isset($body['dateStart'])){
                throw new Exception('No se especifico la fecha');
            }
            if(!isset($body['quantity'])){
                throw new Exception('No se especifico el número de dias');
            }

            $exhibitor =  $this->exhibitorModel->getAll();
            $exhibitorMonitoring =  $this->exhibitorModel->monitoringByCompanyId($companyId, $body['dateStart'], $body['quantity']);

            $res->result = [
                'exhibitor' => $exhibitor,
                'exhibitorMonitoring' => $exhibitorMonitoring,
            ];
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function states(){
        $res = new Result();
        try {
            $postData = file_get_contents("php://input");
            $body = json_decode($postData, true);

            $current = isset($body['current']) ? $body['current'] : 1;
            $exhibitorId = $body['exhibitorId'];

            $res->result = $this->exhibitorHistoryModel->scrollByExhibitorId($exhibitorId, $current);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function table()
    {
        $res = new Result();
        try {
            authorization($this->connection, 'exhibitor_list');
            $page = htmlspecialchars(isset($_GET['page']) ? $_GET['page'] : 1);
            $limit = htmlspecialchars(isset($_GET['limit']) ? $_GET['limit'] : 10);
            $search = htmlspecialchars(isset($_GET['search']) ? $_GET['search'] : '');
            $companyId = $_SESSION[SESS_USER]['company_id'];

            $exhibitor = $this->exhibitorModel->paginateByCompanyId($companyId, $page, $limit, $search);

            $res->view = $this->render('admin/partials/exhibitorTable.php', [
                'exhibitor' => $exhibitor,
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
            authorization($this->connection, 'exhibitor_list');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $res->result = $this->exhibitorModel->getById($body['exhibitorId']);
            $res->success = true;
        } catch (Exception $e) {
            $res->message = $e->getMessage();
        }
        echo json_encode($res);
    }

    public function getByCode()
    {
        $res = new Result();
        try {
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);
            $companyId = $_SESSION[SESS_USER]['company_id'];

            if (!isset($body['code'])) {
                throw new Exception('No se envió el código');
            }

            $code = htmlspecialchars($body['code']);
            if (empty($code)) {
                throw new Exception('Ingrese almenos un codigo');
            }

            $exhibitor = $this->exhibitorModel->getByCodeAndCompanyId($companyId, $code);
            if ($exhibitor == false) {
                throw new Exception('No se encontro ningun resultado');
            }

            $exhibitorView =   $this->render('admin/partials/exhibitorDetail.partial.php', [
                'exhibitor' => $exhibitor,
            ], '', true);

            $res->view = $exhibitorView;
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
            authorization($this->connection, 'exhibitor_create');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);
            $companyId = $_SESSION[SESS_USER]['company_id'];

            $validate = $this->validateInput($body);
            if (!$validate->success) {
                throw new Exception($validate->message);
            }

            $latLong = $body['latitude'] . ',' . $body['longitude'];

            $res->result = $this->exhibitorModel->insert([
                'code' => htmlspecialchars($body['code']),
                'sizeId' => htmlspecialchars($body['sizeId']),
                'geoLocationId' => htmlspecialchars($body['geoLocationId']),
                'latLong' => $latLong,
                'address' => htmlspecialchars($body['address']),
                'customerId' => htmlspecialchars($body['customerId']),
                'companyId' => $companyId,
            ], $_SESSION[SESS_KEY]);
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
            authorization($this->connection, 'exhibitor_update');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $validate = $this->validateInput($body, 'update');
            if (!$validate->success) {
                throw new Exception($validate->message);
            }
            
            $latLong = $body['latitude'] . ',' . $body['longitude'];

            $currentDate = date('Y-m-d H:i:s');
            $this->exhibitorModel->updateById($body['exhibitorId'], [
                'code' => htmlspecialchars($body['code']),
                'size_id' => htmlspecialchars($body['sizeId']),
                'geo_location_id' => htmlspecialchars($body['geoLocationId']),
                'lat_long' => $latLong,
                'address' => htmlspecialchars($body['address']),
                'customer_id' => htmlspecialchars($body['customerId']),

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
            authorization($this->connection, 'exhibitor_delete');
            $postData = file_get_contents('php://input');
            $body = json_decode($postData, true);

            $currentDate = date('Y-m-d H:i:s');
            $this->exhibitorModel->updateById($body['exhibitorId'], [
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

    public function validateInput($body, $type = 'create')
    {
        $res = new Result();
        $res->success = true;

        if ($type == 'create' || $type == 'update') {
            if (($body['geoLocationId'] ?? '') == '') {
                $res->message .= 'Falta especificar la ciudad | ';
                $res->success = false;
            }
            if (($body['code'] ?? '') == '') {
                $res->message .= 'Falta ingresar el código | ';
                $res->success = false;
            }
            if (($body['customerId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el cliente | ';
                $res->success = false;
            }
            if (($body['sizeId'] ?? '') == '') {
                $res->message .= 'Falta especificar el tamañp | ';
                $res->success = false;
            }
        }

        if ($type == 'update') {
            if (($body['exhibitorId'] ?? '') == '') {
                $res->message .= 'Falta ingresar el id cliente | ';
                $res->success = false;
            }
        }

        $res->message = trim(trim($res->message), '|');

        return $res;
    }
}
